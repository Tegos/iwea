<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class AerisWeather extends Helper implements ISiteHelper
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
        $this->url = 'http://api.aerisapi.com/';
        $this->url .= 'forecasts/closest?client_id=rfOXgkJGWy1vAkx9Thurr&';
        $this->url .= '&client_secret=zGs1vvp0eOptZAFFE7cAJZQg9961NFqjaHZF4c5N&limit=14&';
        $this->url .= "limit={$days}&p=";
        $this->url .= "{$lat},{$lon}";
    }


    public function addWeatherData()
    {
        $response = $this->get_web_page($this->url);

        $data = json_decode($response, true);

        $resp = $data['response'][0];

        $periods = $resp['periods'];
        $count = count($periods);


        for ($i = 0; $i < $count; $i++) {
            $period = $periods[$i];

            $data_insert['site_id'] = (int)$this->site_id;
            $data_insert['city_id'] = (int)$this->city_id;
            $data_insert['date'] = $period['validTime'];
            $data_insert['min_temp'] = $period['minTempC'];
            $data_insert['max_temp'] = $period['maxTempC'];

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