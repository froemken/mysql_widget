<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Widget;

use StefanFroemken\MySqlWidget\Domain\Factory\InnoDbStatusFactory;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Settings\SettingDefinition;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\EventDataInterface;
use TYPO3\CMS\Dashboard\Widgets\JavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetContext;
use TYPO3\CMS\Dashboard\Widgets\WidgetRendererInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetResult;

class InnoDbBufferPoolWidget implements WidgetRendererInterface, EventDataInterface, JavaScriptInterface
{
    /**
     * Stateless is not possible here, as we need this widget context in getEventData later
     *
     * @var WidgetContext|null
     */
    private ?WidgetContext $widgetContext = null;

    public function __construct(
        private WidgetConfigurationInterface $configuration,
        private InnoDbStatusFactory $innoDbStatusFactory,
        private BackendViewFactory $backendViewFactory,
    ) {}

    public function getSettingsDefinitions(): array
    {
        return [
            new SettingDefinition(
                key: 'unit',
                type: 'string',
                default: '%',
                label: 'mysql_widget.buffer_pool:label',
                description: 'mysql_widget.buffer_pool:description',
                enum: [
                    '%' => 'Percentage',
                    'mb' => 'MegaByte',
                    'gb' => 'GigaByte',
                ],
            ),
        ];
    }

    public function renderWidget(WidgetContext $context): WidgetResult
    {
        $this->widgetContext = $context;

        $innoDbStatus = $this->innoDbStatusFactory->getInnoDbStatus();

        $view = $this->backendViewFactory->create($context->request);
        $view->assignMultiple([
            'configuration' => $context->configuration,
            'usedData' => $this->formatBytes(
                $innoDbStatus->getInnoDbPoolDataUsedBytes(),
                $innoDbStatus->getInnoDbPoolDataTotalBytes(),
                DisplayUnit::from($context->settings->get('unit')),
            ),
            'totalData' => $this->formatBytes(
                $innoDbStatus->getInnoDbPoolDataTotalBytes(),
                $innoDbStatus->getInnoDbPoolDataTotalBytes(),
                DisplayUnit::from($context->settings->get('unit')),
            ),
        ]);

        return new WidgetResult(
            content: $view->render('Widget/InnoDbBufferPool'),
            label: $context->configuration->getTitle(),
            refreshable: true,
        );
    }

    /**
     * @return array<mixed>
     */
    public function getEventData(): array
    {
        $innoDbStatus = $this->innoDbStatusFactory->getInnoDbStatus();

        return [
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
                                0 => $this->formatBytes(
                                    $innoDbStatus->getInnodbBufferPoolPagesData(),
                                    $innoDbStatus->getInnodbBufferPoolPagesTotal(),
                                    DisplayUnit::from($this->widgetContext->settings->get('unit')),
                                    true,
                                ),
                                1 => $this->formatBytes(
                                    $innoDbStatus->getInnodbBufferPoolPagesMisc(),
                                    $innoDbStatus->getInnodbBufferPoolPagesTotal(),
                                    DisplayUnit::from($this->widgetContext->settings->get('unit')),
                                    true,
                                ),
                                2 => $this->formatBytes(
                                    $innoDbStatus->getInnodbBufferPoolPagesFree(),
                                    $innoDbStatus->getInnodbBufferPoolPagesTotal(),
                                    DisplayUnit::from($this->widgetContext->settings->get('unit')),
                                    true,
                                ),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function formatBytes(int $bytes, int $total, DisplayUnit $displayUnit, bool $returnPlain = false): float|string
    {
        if ($displayUnit->value === 'mb') {
            $unit = ' MB';
            $data = $bytes / 1024 / 1024;
        } elseif ($displayUnit->value === 'gb') {
            $unit = ' GB';
            $data = $bytes / 1024 / 1024 / 1024;
        } else {
            $unit = '%';
            $data = 100 / $total * $bytes;
        }

        if ($returnPlain) {
            return (float)$data;
        }

        return number_format($data, 2, ',', '.') . $unit;
    }

    public function getJavaScriptModuleInstructions(): array
    {
        return [
            JavaScriptModuleInstruction::create('@typo3/dashboard/contrib/chartjs.js'),
            JavaScriptModuleInstruction::create('@typo3/dashboard/chart-initializer.js'),
        ];
    }
}
