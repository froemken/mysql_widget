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
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

class InnoDbStatus implements WidgetInterface, RequestAwareWidgetInterface
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
            'bufferPoolTooSmall' => $this->dataProvider->getInnoDbBufferPoolWaitFree() > 0,
            'readHitRatio' => $this->dataProvider->getInnoDbHitRatio(),
            'handlerReadRatio' => $this->dataProvider->getHandlerReadRatio()
        ]);

        return $view->render('Widget/InnoDbStatus');
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
