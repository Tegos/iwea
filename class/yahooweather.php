<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 13.08.2016
 * Time: 11:16
 */
class YahooWeather extends Helper implements ISiteHelper
{

	var $url;
	private $days = 8;
	var $site_id;
	var $city_id;
	var $model;

	public function __construct()
	{
		$this->model = new  Model();
	}

	public function buildQuery($city)
	{
		$woeid = (int)$city['woeid'];
		$this->url = 'https://query.yahooapis.com/v1/public/yql';
		$this->url .= '?q=' . rawurlencode("select * from weather.forecast where woeid = {$woeid} and u='c' limit {$this->days}");
		$this->url .= '&format=json';
		$this->url .= '&env=' . rawurlencode('store://datatables.org/alltableswithkeys');
		
		//$this->var_dump($this->url);

	}


	public function addWeatherData()
	{
		$response = $this->get_web_page($this->url);
		$data = json_decode($response, true);


		$q = $data['query'];
		$results = $q['results']['channel']['item']['forecast'];

		$count = count($results);


		for ($i = 0; $i < $count; $i++) {
			$period = $results[$i];

			$data_insert['site_id'] = (int)$this->site_id;
			$data_insert['city_id'] = (int)$this->city_id;
			$data_insert['date'] = date('Y-m-d', strtotime($period['date']));
			$data_insert['min_temp'] = $period['low'];
			$data_insert['max_temp'] = $period['high'];

			$this->model->addWeatherRecord($data_insert);
		}

	}

	public function setSiteId($site_id)
	{
		$this->site_id = $site_id;
	}

	public function setCityId($city_id)
	{
		$this->city_id = $city_id;
	}
}