<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Widget;

use StefanFroemken\MySqlWidget\Domain\Factory\HandlerFactory;
use StefanFroemken\MySqlWidget\Domain\Factory\InnoDbStatusFactory;
use StefanFroemken\MySqlWidget\Domain\Model\Handler;
use StefanFroemken\MySqlWidget\Domain\Model\InnoDbStatus;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetContext;
use TYPO3\CMS\Dashboard\Widgets\WidgetRendererInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetResult;

readonly class InnoDbStatusWidget implements WidgetRendererInterface
{
    public function __construct(
        private WidgetConfigurationInterface $configuration,
        private BackendViewFactory $backendViewFactory,
        private HandlerFactory $handlerFactory,
        private InnoDbStatusFactory $innoDbStatusFactory,
    ) {}

    public function getSettingsDefinitions(): array
    {
        return [];
    }

    public function renderWidget(WidgetContext $context): WidgetResult
    {
        $innoDbStatus = $this->innoDbStatusFactory->getInnoDbStatus();
        $handler = $this->handlerFactory->getHandler();

        $view = $this->backendViewFactory->create($context->request);
        $view->assignMultiple([
            'configuration' => $this->configuration,
            'bufferPoolTooSmall' => $innoDbStatus->getInnodbBufferPoolWaitFree() > 0,
            'readHitRatio' => $this->getInnoDbHitRatio($innoDbStatus),
            'handlerReadRatio' => $this->getHandlerReadRatio($handler),
        ]);

        return new WidgetResult(
            content: $view->render('Widget/InnoDbStatus'),
            label: $context->configuration->getTitle(),
            refreshable: true,
        );
    }

    protected function getInnoDbHitRatio(InnoDbStatus $innoDbStatus): float
    {
        $readRequests = $innoDbStatus->getInnodbBufferPoolReadRequests();
        $reads = $innoDbStatus->getInnodbBufferPoolReads();

        if ($readRequests === 0) {
            return 0.0;
        }

        return round($readRequests / ($readRequests + $reads) * 100, 2);
    }

    protected function getHandlerReadRatio(Handler $handler): float
    {
        $handlerReadRndNext = $handler->getHandlerReadRndNext();
        $handlerReadRnd = $handler->getHandlerReadRnd();
        $handlerReadFirst = $handler->getHandlerReadFirst();
        $handlerReadNext = $handler->getHandlerReadNext();
        $handlerReadKey = $handler->getHandlerReadKey();
        $handlerReadPrev = $handler->getHandlerReadPrev();

        return round(
            ($handlerReadRndNext + $handlerReadRnd) / ($handlerReadRndNext + $handlerReadRnd + $handlerReadFirst + $handlerReadNext + $handlerReadKey + $handlerReadPrev),
            2,
        );
    }
}
