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
use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\MySqlWidget\Domain\Factory\InnoDbStatusFactory;
use StefanFroemken\MySqlWidget\Domain\Model\InnoDbStatus;
use StefanFroemken\MySqlWidget\Widget\DisplayUnit;
use StefanFroemken\MySqlWidget\Widget\InnoDbBufferPoolWidget;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Settings\SettingDefinition;
use TYPO3\CMS\Core\Settings\SettingsInterface;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetContext;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class InnoDbBufferPoolWidgetTest extends UnitTestCase
{
    private WidgetConfigurationInterface&MockObject $configurationMock;
    private InnoDbStatusFactory&MockObject $innoDbStatusFactoryMock;
    private InnoDbBufferPoolWidget $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configurationMock = $this->createMock(WidgetConfigurationInterface::class);
        $this->innoDbStatusFactoryMock = $this->createMock(InnoDbStatusFactory::class);

        /** @var BackendViewFactory $backendViewFactory */
        $backendViewFactory = (new \ReflectionClass(BackendViewFactory::class))->newInstanceWithoutConstructor();

        $this->subject = new InnoDbBufferPoolWidget(
            $this->configurationMock,
            $this->innoDbStatusFactoryMock,
            $backendViewFactory,
        );
    }

    #[Test]
    public function getSettingsDefinitionsReturnsUnitSettingDefinition(): void
    {
        $definitions = $this->subject->getSettingsDefinitions();

        self::assertCount(1, $definitions);
        self::assertContainsOnlyInstancesOf(SettingDefinition::class, $definitions);

        $unitDefinition = $definitions[0];
        self::assertSame('unit', $unitDefinition->key);
        self::assertSame('string', $unitDefinition->type);
        self::assertSame('%', $unitDefinition->default);
        self::assertSame([
            '%' => 'Percentage',
            'mb' => 'MegaByte',
            'gb' => 'GigaByte',
        ], $unitDefinition->enum);
    }

    #[Test]
    public function getJavaScriptModuleInstructionsReturnsTwoInstructions(): void
    {
        $instructions = $this->subject->getJavaScriptModuleInstructions();

        self::assertCount(2, $instructions);
        self::assertContainsOnlyInstancesOf(JavaScriptModuleInstruction::class, $instructions);
        self::assertSame('@typo3/dashboard/contrib/chartjs.js', $instructions[0]->getName());
        self::assertSame('@typo3/dashboard/chart-initializer.js', $instructions[1]->getName());
    }

    #[Test]
    public function getEventDataReturnsExpectedGraphConfiguration(): void
    {
        $status = new InnoDbStatus(
            innodbBufferPoolPagesData: 50,
            innodbBufferPoolPagesFree: 30,
            innodbBufferPoolPagesMisc: 20,
            innodbBufferPoolPagesTotal: 100,
            innodbBufferPoolReadRequests: 1000,
            innodbBufferPoolReads: 10,
            innodbBufferPoolWaitFree: 0,
            innodbPageSize: 16384,
        );
        $this->innoDbStatusFactoryMock->method('getInnoDbStatus')->willReturn($status);

        $settingsMock = $this->createMock(SettingsInterface::class);
        $settingsMock->method('get')->with('unit')->willReturn('%');

        $context = new WidgetContext(
            identifier: 'test-widget',
            rawData: [],
            configuration: $this->configurationMock,
            settings: $settingsMock,
            request: $this->createMock(ServerRequestInterface::class),
        );

        $reflection = new \ReflectionProperty(InnoDbBufferPoolWidget::class, 'widgetContext');
        $reflection->setValue($this->subject, $context);

        $eventData = $this->subject->getEventData();

        $expected = [
            'graphConfig' => [
                'type' => 'doughnut',
                'options' => [
                    'maintainAspectRatio' => false,
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ],
                    'tooltips' => [
                        'enabled' => true,
                    ],
                ],
                'data' => [
                    'labels' => [
                        0 => 'Used',
                        1 => 'Misc',
                        2 => 'Free',
                    ],
                    'datasets' => [
                        [
                            'backgroundColor' => WidgetApi::getDefaultChartColors(),
                            'border' => 0,
                            'data' => [
                                0 => 50.0,
                                1 => 20.0,
                                2 => 30.0,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        self::assertSame($expected, $eventData);
    }

    #[Test]
    #[DataProvider('formatBytesDataProvider')]
    public function formatBytesFormatsOutputCorrectly(
        int $bytes,
        int $total,
        DisplayUnit $unit,
        bool $plain,
        float|string $expected,
    ): void {
        $method = new \ReflectionMethod(InnoDbBufferPoolWidget::class, 'formatBytes');

        $result = $method->invoke($this->subject, $bytes, $total, $unit, $plain);

        self::assertSame($expected, $result);
    }

    /**
     * @return array<string, array{0: int, 1: int, 2: DisplayUnit, 3: bool, 4: float|string}>
     */
    public static function formatBytesDataProvider(): array
    {
        return [
            'percentage plain' => [50, 100, DisplayUnit::Percentage, true, 50.0],
            'percentage formatted' => [50, 100, DisplayUnit::Percentage, false, '50,00%'],
            'megabyte plain' => [1048576, 2097152, DisplayUnit::MegaByte, true, 1.0],
            'megabyte formatted' => [1048576, 2097152, DisplayUnit::MegaByte, false, '1,00 MB'],
            'gigabyte plain' => [1073741824, 2147483648, DisplayUnit::GigaByte, true, 1.0],
            'gigabyte formatted' => [1073741824, 2147483648, DisplayUnit::GigaByte, false, '1,00 GB'],
        ];
    }
}
