<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class TheDarkSkyCompany extends Helper implements ISiteHelper
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
        $lat = (float)$city['lat'];
        $lon = (float)$city['lon'];
        $this->url = 'https://api.forecast.io/';
        $this->url .= 'forecast/c91e8152e03c8e173c94908d269aa5c9/';
        $this->url .= "{$lat},{$lon}";
        $this->url .= '?units=si&lang=uk&exclude=[currently,minutely,hourly,alerts,flags]';
        //$this->var_dump($this->url);
    }


    public function addWeatherData()
    {
        $response = $this->get_web_page($this->url);

        $data = json_decode($response, true);

        //$this->var_dump($data);

        $daily = $data['daily'];

        $periods = $daily['data'];
        $count = count($periods);


        for ($i = 0; $i < $count; $i++) {
            $period = $periods[$i];

            $data_insert['site_id'] = (int)$this->site_id;
            $data_insert['city_id'] = (int)$this->city_id;
            $data_insert['date'] = date('Y-m-d', (int)$period['time']);
            $data_insert['min_temp'] = $period['temperatureMin'];
            $data_insert['max_temp'] = $period['temperatureMax'];

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