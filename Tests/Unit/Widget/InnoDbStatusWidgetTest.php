<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Tests\Unit\Widget;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use StefanFroemken\MySqlWidget\Domain\Factory\HandlerFactory;
use StefanFroemken\MySqlWidget\Domain\Factory\InnoDbStatusFactory;
use StefanFroemken\MySqlWidget\Domain\Model\Handler;
use StefanFroemken\MySqlWidget\Domain\Model\InnoDbStatus;
use StefanFroemken\MySqlWidget\Widget\InnoDbStatusWidget;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class InnoDbStatusWidgetTest extends UnitTestCase
{
    private WidgetConfigurationInterface&MockObject $configurationMock;
    private HandlerFactory&MockObject $handlerFactoryMock;
    private InnoDbStatusFactory&MockObject $innoDbStatusFactoryMock;
    private InnoDbStatusWidget $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurationMock = $this->createMock(WidgetConfigurationInterface::class);
        $this->handlerFactoryMock = $this->createMock(HandlerFactory::class);
        $this->innoDbStatusFactoryMock = $this->createMock(InnoDbStatusFactory::class);

        /** @var BackendViewFactory $backendViewFactory */
        $backendViewFactory = (new \ReflectionClass(BackendViewFactory::class))->newInstanceWithoutConstructor();

        $this->subject = new InnoDbStatusWidget(
            $this->configurationMock,
            $backendViewFactory,
            $this->handlerFactoryMock,
            $this->innoDbStatusFactoryMock,
        );
    }

    #[Test]
    public function getSettingsDefinitionsReturnsEmptyArray(): void
    {
        self::assertSame([], $this->subject->getSettingsDefinitions());
    }

    #[Test]
    public function getInnoDbHitRatioReturnsZeroWhenReadRequestsIsZero(): void
    {
        $status = new InnoDbStatus(
            innodbBufferPoolPagesData: 0,
            innodbBufferPoolPagesFree: 0,
            innodbBufferPoolPagesMisc: 0,
            innodbBufferPoolPagesTotal: 0,
            innodbBufferPoolReadRequests: 0,
            innodbBufferPoolReads: 50,
            innodbBufferPoolWaitFree: 0,
            innodbPageSize: 16384,
        );

        $method = new \ReflectionMethod(InnoDbStatusWidget::class, 'getInnoDbHitRatio');
        $result = $method->invoke($this->subject, $status);

        self::assertSame(0.0, $result);
    }

    #[Test]
    #[DataProvider('innoDbHitRatioDataProvider')]
    public function getInnoDbHitRatioCalculatesPercentage(int $readRequests, int $reads, float $expected): void
    {
        $status = new InnoDbStatus(
            innodbBufferPoolPagesData: 0,
            innodbBufferPoolPagesFree: 0,
            innodbBufferPoolPagesMisc: 0,
            innodbBufferPoolPagesTotal: 0,
            innodbBufferPoolReadRequests: $readRequests,
            innodbBufferPoolReads: $reads,
            innodbBufferPoolWaitFree: 0,
            innodbPageSize: 16384,
        );

        $method = new \ReflectionMethod(InnoDbStatusWidget::class, 'getInnoDbHitRatio');
        $result = $method->invoke($this->subject, $status);

        self::assertSame($expected, $result);
    }

    /**
     * @return array<string, array{0: int, 1: int, 2: float}>
     */
    public static function innoDbHitRatioDataProvider(): array
    {
        return [
            '100% hit ratio' => [1000, 0, 100.0],
            '95% hit ratio' => [950, 50, 95.0],
            '99.9% hit ratio' => [999, 1, 99.9],
            'rounded hit ratio' => [9876, 123, 98.77],
        ];
    }

    #[Test]
    #[DataProvider('handlerReadRatioDataProvider')]
    public function getHandlerReadRatioCalculatesRatio(
        int $rndNext,
        int $rnd,
        int $first,
        int $next,
        int $key,
        int $prev,
        float $expected,
    ): void {
        $handler = new Handler(
            handlerReadRndNext: $rndNext,
            handlerReadRnd: $rnd,
            handlerReadFirst: $first,
            handlerReadNext: $next,
            handlerReadKey: $key,
            handlerReadPrev: $prev,
        );

        $method = new \ReflectionMethod(InnoDbStatusWidget::class, 'getHandlerReadRatio');
        $result = $method->invoke($this->subject, $handler);

        self::assertSame($expected, $result);
    }

    /**
     * @return array<string, array{0: int, 1: int, 2: int, 3: int, 4: int, 5: int, 6: float}>
     */
    public static function handlerReadRatioDataProvider(): array
    {
        return [
            '50% ratio' => [100, 50, 25, 75, 50, 0, 0.5],
            '0% ratio' => [0, 0, 100, 100, 50, 50, 0.0],
            '100% ratio' => [200, 100, 0, 0, 0, 0, 1.0],
            'rounded ratio' => [150, 30, 40, 60, 20, 10, 0.58],
        ];
    }
}
