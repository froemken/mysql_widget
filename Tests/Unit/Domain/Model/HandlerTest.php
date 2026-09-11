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
use StefanFroemken\MySqlWidget\Domain\Model\Handler;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class HandlerTest extends UnitTestCase
{
    #[Test]
    public function gettersReturnExpectedValues(): void
    {
        $handler = new Handler(
            handlerReadRndNext: 10,
            handlerReadRnd: 20,
            handlerReadFirst: 30,
            handlerReadNext: 40,
            handlerReadKey: 50,
            handlerReadPrev: 60,
        );

        self::assertSame(10, $handler->getHandlerReadRndNext());
        self::assertSame(20, $handler->getHandlerReadRnd());
        self::assertSame(30, $handler->getHandlerReadFirst());
        self::assertSame(40, $handler->getHandlerReadNext());
        self::assertSame(50, $handler->getHandlerReadKey());
        self::assertSame(60, $handler->getHandlerReadPrev());
    }
}
