<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\MySqlWidget\Domain\Model\InnoDbStatus;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class InnoDbStatusTest extends UnitTestCase
{
    #[Test]
    public function gettersReturnExpectedValues(): void
    {
        $status = new InnoDbStatus(
            innodbBufferPoolPagesData: 100,
            innodbBufferPoolPagesFree: 50,
            innodbBufferPoolPagesMisc: 20,
            innodbBufferPoolPagesTotal: 170,
            innodbBufferPoolReadRequests: 1000,
            innodbBufferPoolReads: 50,
            innodbBufferPoolWaitFree: 5,
            innodbPageSize: 16384,
        );

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
    public function getInnoDbPoolDataTotalBytesCalculatesBytes(): void
    {
        $status = new InnoDbStatus(
            innodbBufferPoolPagesData: 100,
            innodbBufferPoolPagesFree: 50,
            innodbBufferPoolPagesMisc: 20,
            innodbBufferPoolPagesTotal: 170,
            innodbBufferPoolReadRequests: 1000,
            innodbBufferPoolReads: 50,
            innodbBufferPoolWaitFree: 0,
            innodbPageSize: 16384,
        );

        self::assertSame(170 * 16384, $status->getInnoDbPoolDataTotalBytes());
    }

    #[Test]
    public function getInnoDbPoolDataUsedBytesCalculatesSumOfDataAndMiscBytes(): void
    {
        $status = new InnoDbStatus(
            innodbBufferPoolPagesData: 100,
            innodbBufferPoolPagesFree: 50,
            innodbBufferPoolPagesMisc: 20,
            innodbBufferPoolPagesTotal: 170,
            innodbBufferPoolReadRequests: 1000,
            innodbBufferPoolReads: 50,
            innodbBufferPoolWaitFree: 0,
            innodbPageSize: 16384,
        );

        $expectedUsedBytes = (100 * 16384) + (20 * 16384);
        self::assertSame($expectedUsedBytes, $status->getInnoDbPoolDataUsedBytes());
    }
}
