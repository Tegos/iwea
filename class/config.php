<?php

final class  Config
{

	private static $data =
		array(
			'hostname' => 'localhost',
			'username' => 'noisy2',
			'password' => 'tegos',
			'database' => 'noisy2_weather',
			'default_city' => 3,
			'default_site' => 2,
			'start_date' => '2016-05-12',
			'domen' => 'https://iwea.ml',
			'state_file' => '/../data/state',
			'sync_file' => '/../data/sync.log',
			'timeZone' => 'Europe/Kiev'
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