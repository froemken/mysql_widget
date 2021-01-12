<?php
declare(strict_types = 1);

namespace StefanFroemken\MySqlWidget\Widgets;

use StefanFroemken\MySqlWidget\DataProvider\InnoDbDataProvider;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\EventDataInterface;
use TYPO3\CMS\Dashboard\Widgets\RequireJsModuleInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class InnoDbBufferPool implements WidgetInterface, EventDataInterface, AdditionalCssInterface, RequireJsModuleInterface
{
    /**
     * @var WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var InnoDbDataProvider
     */
    private $dataProvider;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        InnoDbDataProvider $dataProvider
    ) {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->dataProvider = $dataProvider;
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate('Widget/InnoDbBufferPool');
        $this->view->assignMultiple([
            'configuration' => $this->configuration,
            'usedData' => $this->dataProvider->getInnoDbPoolDataUsed(),
            'totalData' => $this->dataProvider->getInnoDbPoolDataTotal()
        ]);

        return $this->view->render();
    }

    public function getEventData(): array
    {
        return [
            'graphConfig' => [
                'type' => 'doughnut',
                'options' => [
                    'maintainAspectRatio' => false,
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom'
                    ],
                    'tooltips' => [
                        'enabled' => true
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Usage in %'
                    ]
                ],
                'data' => $this->dataProvider->getChartData(),
            ],
        ];
    }

    public function getCssFiles(): array
    {
        return [
            'EXT:dashboard/Resources/Public/Css/Contrib/chart.css'
        ];
    }

    public function getRequireJsModules(): array
    {
        return [
            'TYPO3/CMS/Dashboard/Contrib/chartjs',
            'TYPO3/CMS/Dashboard/ChartInitializer',
        ];
    }
}
