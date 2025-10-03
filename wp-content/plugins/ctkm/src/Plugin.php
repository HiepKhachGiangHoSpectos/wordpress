<?php

namespace CTKM;

use CTKM\Services\ServiceLoader;

class Plugin
{
    private static $instance;
    private $file;

    private function __construct($file)
    {
        $this->file = $file;
    }

    // (Trước kia: run_ctkm_plugin)
    public static function get_instance($file = null)
    {
        if (!self::$instance) {
            self::$instance = new self($file);
        }
        return self::$instance;
    }

    public function run()
    {
        // (Trước kia: activate_ctkm_plugin)
        register_activation_hook($this->file, [Activation::class, 'activate']);
        register_deactivation_hook($this->file, [Activation::class, 'deactivate']);

        // Load các service (PostType, Admin, Frontend)
        (new ServiceLoader())->init();
    }
}
