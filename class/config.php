<?php

final class  Config
{

    private static $data =
        array(
            'hostname' => 'localhost',
            'username' => 'noisy2',
            'password' => 'tegos',
            'database' => 'noisy2_weather',
            'default_city' => 3
        );

    public static function get($key)
    {
        if (isset(self::$data[$key])) {
            return self::$data[$key];
        } else {
            return 0;
        }
    }


}