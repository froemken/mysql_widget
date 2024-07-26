<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Widgets;

use Psr\Http\Message\ServerRequestInterface;
use StefanFroemken\MySqlWidget\DataProvider\InnoDbDataProvider;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Dashboard\Widgets\EventDataInterface;
use TYPO3\CMS\Dashboard\Widgets\JavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

class InnoDbBufferPool implements WidgetInterface, EventDataInterface, JavaScriptInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly InnoDbDataProvider $dataProvider,
        private readonly array $options = []
    ) {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request);
        $view->assignMultiple([
            'configuration' => $this->configuration,
            'usedData' => $this->dataProvider->getInnoDbPoolDataUsed(),
            'totalData' => $this->dataProvider->getInnoDbPoolDataTotal()
        ]);

        return $view->render('Widget/InnoDbBufferPool');
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

    public function getJavaScriptModuleInstructions(): array
    {
        return [
            JavaScriptModuleInstruction::create('@typo3/dashboard/contrib/chartjs.js'),
            JavaScriptModuleInstruction::create('@typo3/dashboard/chart-initializer.js'),
        ];
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
