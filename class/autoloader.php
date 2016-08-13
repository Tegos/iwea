<?php

class AutoLoader
{
    public function __construct()
    {
        spl_autoload_register(array($this, 'loader'));
        $this->setSetting();
    }

    public function setSetting()
    {
        error_reporting(E_ALL);
        define('DIR_CACHE', 'file/');
        ini_set('memory_limit', '256M');
        define('HOME', str_replace('class', '', __DIR__));
    }

    private function loader($className)
    {
        if (file_exists(__DIR__ . '/' . strtolower($className) . '.php'))
            require(__DIR__ . '/' . strtolower($className) . '.php');
    }
}