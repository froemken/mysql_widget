<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\MySqlWidget\Widgets;

use StefanFroemken\MySqlWidget\DataProvider\InnoDbDataProvider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class InnoDbStatus implements WidgetInterface
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
        $this->view->setTemplate('Widget/InnoDbStatus');
        $this->view->assignMultiple([
            'configuration' => $this->configuration,
            'bufferPoolTooSmall' => $this->dataProvider->getInnoDbBufferPoolWaitFree() > 0,
            'readHitRatio' => $this->dataProvider->getInnoDbHitRatio(),
            'handlerReadRatio' => $this->dataProvider->getHandlerReadRatio()
        ]);

        return $this->view->render();
    }
}
