<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Domain\Factory;

use Doctrine\DBAL\Exception;
use StefanFroemken\MySqlWidget\Domain\Model\InnoDbStatus;
use TYPO3\CMS\Core\Database\ConnectionPool;

readonly class InnoDbStatusFactory
{
    public function __construct(
        protected ConnectionPool $connectionPool,
    ) {}

    public function getInnoDbStatus(): ?InnoDbStatus
    {
        try {
            $connection = $this->connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
            $queryResult = $connection->executeQuery('SHOW GLOBAL STATUS LIKE \'Innodb_%\'');

            $innoDbStatus = [];
            while ($row = $queryResult->fetchAssociative()) {
                $innoDbStatus[$row['Variable_name']] = $row['Value'];
            }
        } catch (Exception $e) {
            return null;
        }

        return new InnoDbStatus(
            innodbBufferPoolPagesData: (int)($innoDbStatus['Innodb_buffer_pool_pages_data'] ?? 0),
            innodbBufferPoolPagesFree: (int)($innoDbStatus['Innodb_buffer_pool_pages_free'] ?? 0),
            innodbBufferPoolPagesMisc: (int)($innoDbStatus['Innodb_buffer_pool_pages_misc'] ?? 0),
            innodbBufferPoolPagesTotal: (int)($innoDbStatus['Innodb_buffer_pool_pages_total'] ?? 0),
            innodbBufferPoolReadRequests: (int)($innoDbStatus['Innodb_buffer_pool_read_requests'] ?? 0),
            innodbBufferPoolReads: (int)($innoDbStatus['Innodb_buffer_pool_reads'] ?? 0),
            innodbBufferPoolWaitFree: (int)($innoDbStatus['Innodb_buffer_pool_wait_free'] ?? 0),
            innodbPageSize: (int)($innoDbStatus['Innodb_page_size'] ?? 0),
        );
    }
}
