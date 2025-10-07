<?php
namespace CTKM\Core;

class ServiceLoader {
    protected array $services = [];

    public function register(array $services) {
        foreach ($services as $class) {
            $service = new $class();
            if (method_exists($service, 'register')) {
                $service->register();
            }
            $this->services[] = $service;
        }
    }
}
