<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\DataProvider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class InnoDbDataProvider implements ChartDataProviderInterface
{
    /**
     * @var array<string, array<string, int>>
     */
    protected array $innoDbStatus = [];

    /**
     * @var array<string, int>
     */
    protected array $handlerStatus = [];

    /**
     * @return array{labels: array<int, string>, datasets: array<mixed>}
     */
    public function getChartData(): array
    {
        return [
            'labels' => [
                0 => 'Used',
                1 => 'Misc',
                2 => 'Free',
            ],
            'datasets' => [
                [
                    'backgroundColor' => WidgetApi::getDefaultChartColors(),
                    'border' => 0,
                    'data' => $this->getInnoDbChartData(),
                ],
            ],
        ];
    }

    /**
     * @return array<int, mixed> An array of innodb buffer pool pages with keys 0 for 'data', 1 for 'misc', and 2 for 'free'
     */
    protected function getInnoDbChartData(): array
    {
        $innoDbStatus = $this->getInnoDbStatus();

        return [
            0 => $innoDbStatus['Innodb_buffer_pool_pages_data'],
            1 => $innoDbStatus['Innodb_buffer_pool_pages_misc'],
            2 => $innoDbStatus['Innodb_buffer_pool_pages_free'],
        ];
    }

    /**
     * @return array|int[][]
     */
    protected function getInnoDbStatus(): array
    {
        if ($this->innoDbStatus === []) {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName(
                ConnectionPool::DEFAULT_CONNECTION_NAME,
            );
            $queryResult = $connection->executeQuery('SHOW GLOBAL STATUS LIKE \'Innodb_%\'');

            $innoDbStatus = [];
            while ($row = $queryResult->fetchAssociative()) {
                $innoDbStatus[$row['Variable_name']] = $row['Value'];
            }

            $this->innoDbStatus = $innoDbStatus;
        }

        return $this->innoDbStatus;
    }

    /**
     * @return array|int[]
     */
    protected function getHandlerStatus(): array
    {
        if (empty($this->handlerStatus)) {
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName(
                ConnectionPool::DEFAULT_CONNECTION_NAME,
            );
            $queryResult = $connection->executeQuery('SHOW GLOBAL STATUS LIKE \'Handler_%\'');

            $handlerStatus = [];
            while ($row = $queryResult->fetchAssociative()) {
                $handlerStatus[$row['Variable_name']] = $row['Value'];
            }

            $this->handlerStatus = $handlerStatus;
        }

        return $this->handlerStatus;
    }

    public function getInnoDbPageSize(): int
    {
        return (int)$this->getInnoDbStatus()['Innodb_page_size'];
    }

    public function getInnoDbPoolDataTotal(): int
    {
        return (int)$this->getInnoDbStatus()['Innodb_buffer_pool_pages_total'] * $this->getInnoDbPageSize();
    }

    public function getInnoDbPoolDataUsed(): int
    {
        $data = (int)$this->getInnoDbStatus()['Innodb_buffer_pool_pages_data'] * $this->getInnoDbPageSize();
        $misc = (int)$this->getInnoDbStatus()['Innodb_buffer_pool_pages_misc'] * $this->getInnoDbPageSize();

        return $data + $misc;
    }

    public function getInnoDbBufferPoolWaitFree(): int
    {
        return (int)$this->getInnoDbStatus()['Innodb_buffer_pool_wait_free'];
    }

    public function getInnoDbHitRatio(): float
    {
        $readRequests = (int)$this->getInnoDbStatus()['Innodb_buffer_pool_read_requests'];
        $reads = (int)$this->getInnoDbStatus()['Innodb_buffer_pool_reads'];

        if ($readRequests === 0) {
            return 0.0;
        }

        return round($readRequests / ($readRequests + $reads) * 100, 2);
    }

    public function getHandlerReadRatio(): float
    {
        $handlerReadRndNext = (int)$this->getHandlerStatus()['Handler_read_rnd_next'];
        $handlerReadRnd = (int)$this->getHandlerStatus()['Handler_read_rnd'];
        $handlerReadFirst = (int)$this->getHandlerStatus()['Handler_read_first'];
        $handlerReadNext = (int)$this->getHandlerStatus()['Handler_read_next'];
        $handlerReadKey = (int)$this->getHandlerStatus()['Handler_read_key'];
        $handlerReadPrev = (int)$this->getHandlerStatus()['Handler_read_prev'];

        return round(
            ($handlerReadRndNext + $handlerReadRnd) / ($handlerReadRndNext + $handlerReadRnd + $handlerReadFirst + $handlerReadNext + $handlerReadKey + $handlerReadPrev),
            2,
        );
    }
}
