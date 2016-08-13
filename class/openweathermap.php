<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class OpenWeatherMap extends Helper implements ISiteHelper
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
        $days = $this->days;
        $lat = (float)$city['lat'];
        $lon = (float)$city['lon'];
        $this->url = 'http://api.openweathermap.org/data/2.5/';
        $this->url .= "forecast/daily?lat={$lat}&lon={$lon}";
        $this->url .= "&cnt={$days}&mode=json&appid=658e0a6fcc706a90582ad5cd96dab7f5&&units=metric";
        //$this->var_dump($this->url);
    }


    public function addWeatherData()
    {
        $response = $this->get_web_page($this->url);

        $data = json_decode($response, true);

        //$this->var_dump($data);

        $periods = $data['list'];
        $count = count($periods);

        $begin_date = new DateTime();
        for ($i = 0; $i < $count; $i++) {
            $period = $periods[$i];

            if ($i == 0) {
                $dd = $begin_date;
            } else {
                $dd = $begin_date->modify('+1 day');
            }

            $data_insert['site_id'] = (int)$this->site_id;
            $data_insert['city_id'] = (int)$this->city_id;
            $data_insert['date'] = $dd->format('Y-m-d');
            $data_insert['min_temp'] = $period['temp']['min'];
            $data_insert['max_temp'] = $period['temp']['max'];

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