<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Tests\Unit\Widget;

use PHPUnit\Framework\Attributes\Test;
use StefanFroemken\MySqlWidget\Widget\DisplayUnit;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DisplayUnitTest extends UnitTestCase
{
    #[Test]
    public function enumCasesHaveExpectedValues(): void
    {
        self::assertSame('mb', DisplayUnit::MegaByte->value);
        self::assertSame('gb', DisplayUnit::GigaByte->value);
        self::assertSame('%', DisplayUnit::Percentage->value);
    }

    #[Test]
    public function enumCanBeCreatedFromValues(): void
    {
        self::assertSame(DisplayUnit::MegaByte, DisplayUnit::from('mb'));
        self::assertSame(DisplayUnit::GigaByte, DisplayUnit::from('gb'));
        self::assertSame(DisplayUnit::Percentage, DisplayUnit::from('%'));
    }
}
