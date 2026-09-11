<?php

/*
 * This file is part of the package stefanfroemken/mysql-widget.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Boilerplate for a unit test phpunit boostrap file.
 *
 * This file is loosely maintained within TYPO3 testing-framework, extensions
 * are encouraged to not use it directly, but to copy it to an own place,
 * usually in parallel to a UnitTests.xml file.
 *
 * This file is defined in UnitTests.xml and called by phpunit
 * before instantiating the test suites.
 *
 * It is adapted to what extensions usually need in unit tests:
 * Some initialize of basics like constants, along with a simple
 * class loader and a minimal mock TYPO3_CONF_VARS.
 *
 * If further core test logic is needed, consider looking at the
 * according script within TYPO3 core's Build/Scripts directory and
 * adapt to extensions needs.
 */

use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Configuration\ConfigurationManager;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Package\UnitTestPackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\SystemEnvironmentBuilder;
use TYPO3\TestingFramework\Core\Testbase;

(static function () {
    $testbase = new Testbase();

    // These if's are for core testing (package typo3/cms) only. cms-composer-installer does
    // not create the autoload-include.php file that sets these env vars and sets composer
    // mode to true. We therefore set these env vars manually for extensions.
    $composerMode = true;
    if (!getenv('TYPO3_PATH_ROOT')) {
        putenv('TYPO3_PATH_ROOT=' . rtrim($testbase->getWebRoot(), '/'));
    }

    if (!getenv('TYPO3_PATH_WEB')) {
        putenv('TYPO3_PATH_WEB=' . rtrim($testbase->getWebRoot(), '/'));
    }

    // A bit ugly, but CoreHttpApplication was introduced in v12 and dropped with v13.
    // TestingFramework v8 supports both v12 and v13, so we need to know what to call.
    // CoreHttpApplication is only present in v12, so check on that.
    // @todo: Remove if branch when dropping support for v12
    // @todo: Remove else branch when dropping support for v12
    $hasConsolidatedHttpEntryPoint = class_exists(CoreHttpApplication::class);
    if ($hasConsolidatedHttpEntryPoint) {
        SystemEnvironmentBuilder::run(0, \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_CLI, $composerMode);
    } else {
        $requestType = \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_BE | \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_CLI;
        SystemEnvironmentBuilder::run(0, $requestType, $composerMode);
    }

    $testbase->createDirectory(Environment::getPublicPath() . '/typo3conf/ext');
    $testbase->createDirectory(Environment::getPublicPath() . '/typo3temp/assets');
    $testbase->createDirectory(Environment::getPublicPath() . '/typo3temp/var/tests');
    $testbase->createDirectory(Environment::getPublicPath() . '/typo3temp/var/transient');

    // Retrieve an instance of class loader and inject to core bootstrap
    $classLoader = require $testbase->getPackagesPath() . '/autoload.php';
    Bootstrap::initializeClassLoader($classLoader);

    // Initialize default TYPO3_CONF_VARS
    $configurationManager = new ConfigurationManager();
    $GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();

    $cache = new PhpFrontend(
        'core',
        new NullBackend('production', []),
    );
    $packageManager = Bootstrap::createPackageManager(
        UnitTestPackageManager::class,
        Bootstrap::createPackageCache($cache),
    );

    GeneralUtility::setSingletonInstance(PackageManager::class, $packageManager);
    ExtensionManagementUtility::setPackageManager($packageManager);

    $testbase->dumpClassLoadingInformation();

    GeneralUtility::purgeInstances();
})();
