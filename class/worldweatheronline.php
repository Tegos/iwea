<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class WorldWeatherOnline extends Helper implements ISiteHelper
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


        $this->url = 'http://api.worldweatheronline.com/';
        $this->url .= 'premium/v1/weather.ashx?key=1dd4ccb28dd6497ca52195520160605&format=json&';
        $this->url .= "num_of_days={$days}&";

        $this->url .= "q={$lat},{$lon}&";
        $this->url .= 'includelocation=yes';
        //$this->var_dump($this->url);
    }


    public function addWeatherData()
    {
        $response = $this->get_web_page($this->url);

        $data = json_decode($response, true);

        $periods = $data['data']['weather'];
        $count = count($periods);

        for ($i = 0; $i < $count; $i++) {
            $period = $periods[$i];

            $data_insert['site_id'] = (int)$this->site_id;
            $data_insert['city_id'] = (int)$this->city_id;
            $data_insert['date'] = $period['date'];
            $data_insert['min_temp'] = $period['mintempC'];
            $data_insert['max_temp'] = $period['maxtempC'];

            //$this->var_dump($data_insert);
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