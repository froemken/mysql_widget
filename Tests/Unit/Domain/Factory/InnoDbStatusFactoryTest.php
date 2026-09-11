<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Tests\Unit\Domain\Factory;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\MySqlWidget\Domain\Factory\InnoDbStatusFactory;
use StefanFroemken\MySqlWidget\Domain\Model\InnoDbStatus;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class InnoDbStatusFactoryTest extends UnitTestCase
{
    #[Test]
    public function getInnoDbStatusReturnsPopulatedStatusOnSuccess(): void
    {
        $result = $this->createMock(Result::class);
        $result->expects($this->exactly(9))
            ->method('fetchAssociative')
            ->willReturn(
                ['Variable_name' => 'Innodb_buffer_pool_pages_data', 'Value' => '100'],
                ['Variable_name' => 'Innodb_buffer_pool_pages_free', 'Value' => '50'],
                ['Variable_name' => 'Innodb_buffer_pool_pages_misc', 'Value' => '20'],
                ['Variable_name' => 'Innodb_buffer_pool_pages_total', 'Value' => '170'],
                ['Variable_name' => 'Innodb_buffer_pool_read_requests', 'Value' => '1000'],
                ['Variable_name' => 'Innodb_buffer_pool_reads', 'Value' => '50'],
                ['Variable_name' => 'Innodb_buffer_pool_wait_free', 'Value' => '5'],
                ['Variable_name' => 'Innodb_page_size', 'Value' => '16384'],
                false,
            );

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with("SHOW GLOBAL STATUS LIKE 'Innodb_%'")
            ->willReturn($result);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $factory = new InnoDbStatusFactory($connectionPool);
        $status = $factory->getInnoDbStatus();

        self::assertInstanceOf(InnoDbStatus::class, $status);
        self::assertSame(100, $status->getInnodbBufferPoolPagesData());
        self::assertSame(50, $status->getInnodbBufferPoolPagesFree());
        self::assertSame(20, $status->getInnodbBufferPoolPagesMisc());
        self::assertSame(170, $status->getInnodbBufferPoolPagesTotal());
        self::assertSame(1000, $status->getInnodbBufferPoolReadRequests());
        self::assertSame(50, $status->getInnodbBufferPoolReads());
        self::assertSame(5, $status->getInnodbBufferPoolWaitFree());
        self::assertSame(16384, $status->getInnodbPageSize());
    }

    #[Test]
    public function getInnoDbStatusReturnsNullWhenDatabaseExceptionOccurs(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->willThrowException($this->createMock(Exception::class));

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $factory = new InnoDbStatusFactory($connectionPool);

        self::assertNull($factory->getInnoDbStatus());
    }
}
