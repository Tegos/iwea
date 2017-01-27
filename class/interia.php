<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class Interia extends Helper implements ISiteHelper
{

	private $url;
	private $days = 8;
	private $site_id;
	private $city_id;
	private $model;

	public function __construct()
	{
		$this->model = new  Model();
	}

	public function buildQuery($city)
	{
		$city_name = $city['name_pl'];
		$cid_pl = $city['cid_pl'];

		$this->url = 'https://pogoda.interia.pl/';
		$this->url .= "prognoza-dlugoterminowa-{$city_name},cId,{$cid_pl}";

		//$this->var_dump($this->url);
	}


	public function addWeatherData()
	{
		$content = $this->get_web_page($this->url);
		$html = new simple_html_dom();
		$html->load($content);
		//$html = simple_html_dom::str_get_html($content);


		if (!empty($html) && is_object($html)) {

			$lists = $html->find('.weather-forecast-longterm-list', 0);

			$lies = $lists->children();

			$i = 0;
			foreach ($lies as $li) {
				if ($li->tag == 'li') {
					$data_insert = array();

					$date = trim($li->find('.date', 0)->plaintext);
					$day = trim($li->find('.day', 0)->plaintext);

					$date_w = $this->getIndexOfPolishDay($day);

					$date .= '.' . date('Y');

					$DateTime = DateTime::createFromFormat('d.m.Y', $date);
					$cur_date_w = $DateTime->format('w');

					if ($date_w != $cur_date_w) {
						$DateTime->modify('+1 year');
					}


					$data_insert['site_id'] = (int)$this->site_id;
					$data_insert['city_id'] = (int)$this->city_id;
					$data_insert['date'] = $DateTime->format('Y-m-d');

					$max = trim($li->find('.weather-forecast-longterm-list-entry-forecast-temp', 0)->plaintext);
					$min = trim($li->find('.weather-forecast-longterm-list-entry-forecast-lowtemp', 0)->plaintext);


					$data_insert['min_temp'] = (int)trim($min, '°C');
					$data_insert['max_temp'] = (int)trim($max, '°C');;

					//$this->var_dump($data_insert);
					$this->model->addWeatherRecord($data_insert);

					$i++;

				}
			}

			$html->clear();
		} else {
			trigger_error('Не доступний контетн');
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