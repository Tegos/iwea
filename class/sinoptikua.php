<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class SinoptikUa extends Helper implements ISiteHelper
{

	var $url;
	var $days = 8;
	var $site_id;
	var $city_id;
	var $model;

	public function __construct()
	{
		$this->model = new  Model();
	}

	public function buildQuery($city)
	{
		$city_name = $city['name_sinoptik'];

		$this->url = 'https://ua.sinoptik.ua/';
		$this->url .= "погода-{$city_name}/10-днів";
		//var_dump($this->url);
	}


	public function addWeatherData()
	{

		$content = $this->get_web_page($this->url);


		$html = simple_html_dom::str_get_html($content);


		$lists = $html->getElementById('blockDays');

		$tabs = $lists->find('.tabs', 0);

		$days = $tabs->find('.main');

		$begin_date = new DateTime();
		$i = 0;
		foreach ($days as $day) {
			$data_insert = array();

			if ($i == 0) {
				$dd = $begin_date;
			} else {
				$dd = $begin_date->modify('+1 day');
			}

			$data_insert['site_id'] = (int)$this->site_id;
			$data_insert['city_id'] = (int)$this->city_id;
			$data_insert['date'] = $dd->format('Y-m-d');

			$max = trim($day->find('.max span', 0)->plaintext);
			$min = trim($day->find('.min span', 0)->plaintext);


			$data_insert['min_temp'] = (int)trim($min, '°C');
			$data_insert['max_temp'] = (int)trim($max, '°C');;

			//$this->var_dump($data_insert);
			$this->model->addWeatherRecord($data_insert);

			$i++;
			if ($i > $this->days) break;
		}

		$html->clear();

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