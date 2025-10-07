<?php
namespace CTKM\Core;

class Plugin {
    protected static $instance = null;
    public $serviceLoader;

    private function __construct() {
        $this->serviceLoader = new ServiceLoader();
        $this->registerHooks();
    }

    public static function init() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function registerHooks() {
        $this->serviceLoader->register([
            \CTKM\Hooks\AdminHooks::class,
            \CTKM\Hooks\FrontendHooks::class,
            \CTKM\Hooks\AjaxHooks::class,
        ]);
    }
}
