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
use StefanFroemken\MySqlWidget\Domain\Model\Handler;
use TYPO3\CMS\Core\Database\ConnectionPool;

readonly class HandlerFactory
{
    public function __construct(
        protected ConnectionPool $connectionPool,
    ) {}

    public function getHandler(): ?Handler
    {
        try {
            $connection = $this->connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
            $queryResult = $connection->executeQuery('SHOW GLOBAL STATUS LIKE \'Handler_%\'');

            $innoDbStatus = [];
            while ($row = $queryResult->fetchAssociative()) {
                $innoDbStatus[$row['Variable_name']] = $row['Value'];
            }
        } catch (Exception $e) {
            return null;
        }

        return new Handler(
            handlerReadRndNext: (int)($innoDbStatus['Handler_read_rnd_next'] ?? 0),
            handlerReadRnd: (int)($innoDbStatus['Handler_read_rnd'] ?? 0),
            handlerReadFirst: (int)($innoDbStatus['Handler_read_first'] ?? 0),
            handlerReadNext: (int)($innoDbStatus['Handler_read_next'] ?? 0),
            handlerReadKey: (int)($innoDbStatus['Handler_read_key'] ?? 0),
            handlerReadPrev: (int)($innoDbStatus['Handler_read_prev'] ?? 0),
        );
    }
}
