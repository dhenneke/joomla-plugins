<?php

declare(strict_types=1);

namespace {
    $repoRoot = __DIR__;
    $joomlaRoot = $repoRoot . '/.joomla';
    $joomlaBootstrap = $joomlaRoot . '/libraries/bootstrap.php';
    $joomlaVendorAutoload = $joomlaRoot . '/libraries/vendor/autoload.php';
    $spPageBuilderRoot = $repoRoot . '/.sppagebuilder';
    $spPageBuilderAddons = $spPageBuilderRoot . '/site/parser/addons.php';
    $spPageBuilderConfig = $spPageBuilderRoot . '/site/builder/classes/config.php';

    if (!file_exists($joomlaBootstrap) || !file_exists($joomlaVendorAutoload)) {
        throw new RuntimeException('A Joomla installation is required in .joomla for PHPStan analysis.');
    }

    if (!defined('_JEXEC')) {
        // Required by Joomla and SP Page Builder file guards during manual bootstrap.
        define('_JEXEC', 1);
    }

    // PHPStan loads framework files outside Joomla's normal entrypoint, so the core path
    // constants must exist before requiring the Joomla bootstrap.
    foreach ([
        'JPATH_BASE',
        'JPATH_ROOT',
        'JPATH_SITE',
        'JPATH_ADMINISTRATOR',
        'JPATH_PLUGINS',
    ] as $constant) {
        if (!defined($constant)) {
            define($constant, $joomlaRoot);
        }
    }

    if (!defined('JPATH_LIBRARIES')) {
        define('JPATH_LIBRARIES', $joomlaRoot . '/libraries');
    }

    require_once $joomlaBootstrap;

    // Use real SP Page Builder classes when a local extracted copy is available.
    if (file_exists($spPageBuilderAddons)) {
        require_once $spPageBuilderAddons;
    }

    if (file_exists($spPageBuilderConfig)) {
        require_once $spPageBuilderConfig;
    }
}

namespace {
    // Keep minimal fallback types for CI, where the paid SP Page Builder sources are absent.
    // Defining them at runtime keeps the IDE focused on the real classes when .sppagebuilder exists.
    if (!class_exists('SppagebuilderAddons', false)) {
        eval(<<<'PHP'
class SppagebuilderAddons
{
    /** @var object */
    public $addon;

    public function __construct()
    {
        $this->addon = (object) ['settings' => (object) []];
    }
}
PHP);
    }

    if (!class_exists('SpAddonsConfig', false)) {
        eval(<<<'PHP'
class SpAddonsConfig
{
    public static function addonConfig(array $config): void
    {
    }
}
PHP);
    }
}
