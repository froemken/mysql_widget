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
use StefanFroemken\MySqlWidget\Domain\Factory\HandlerFactory;
use StefanFroemken\MySqlWidget\Domain\Model\Handler;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class HandlerFactoryTest extends UnitTestCase
{
    #[Test]
    public function getHandlerReturnsPopulatedHandlerOnSuccess(): void
    {
        $result = $this->createMock(Result::class);
        $result->expects($this->exactly(7))
            ->method('fetchAssociative')
            ->willReturn(
                ['Variable_name' => 'Handler_read_rnd_next', 'Value' => '11'],
                ['Variable_name' => 'Handler_read_rnd', 'Value' => '22'],
                ['Variable_name' => 'Handler_read_first', 'Value' => '33'],
                ['Variable_name' => 'Handler_read_next', 'Value' => '44'],
                ['Variable_name' => 'Handler_read_key', 'Value' => '55'],
                ['Variable_name' => 'Handler_read_prev', 'Value' => '66'],
                false,
            );

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('executeQuery')
            ->with("SHOW GLOBAL STATUS LIKE 'Handler_%'")
            ->willReturn($result);

        $connectionPool = $this->createMock(ConnectionPool::class);
        $connectionPool->expects($this->once())
            ->method('getConnectionByName')
            ->with(ConnectionPool::DEFAULT_CONNECTION_NAME)
            ->willReturn($connection);

        $factory = new HandlerFactory($connectionPool);
        $handler = $factory->getHandler();

        self::assertInstanceOf(Handler::class, $handler);
        self::assertSame(11, $handler->getHandlerReadRndNext());
        self::assertSame(22, $handler->getHandlerReadRnd());
        self::assertSame(33, $handler->getHandlerReadFirst());
        self::assertSame(44, $handler->getHandlerReadNext());
        self::assertSame(55, $handler->getHandlerReadKey());
        self::assertSame(66, $handler->getHandlerReadPrev());
    }

    #[Test]
    public function getHandlerReturnsNullWhenDatabaseExceptionOccurs(): void
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

        $factory = new HandlerFactory($connectionPool);

        self::assertNull($factory->getHandler());
    }
}
