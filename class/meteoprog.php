<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class Meteoprog extends Helper implements ISiteHelper
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
        $city = $city['name_tr'];
        $this->url = 'http://www.meteoprog.ua/ua/review/';
        $this->url .= $city . '/?_pjax=div%23reviewforecast_pjax_container';
        //var_dump($this->url);
    }


    public function addWeatherData()
    {
        $content = $this->get_web_page($this->url);
        $html = simple_html_dom::str_get_html($content);

        if (is_object($html) && count($html) > 0) {

            $scripts = $html->find('.detailBlockWeather script', 0);


            $data_temperature = trim($scripts->innertext);
            $data_temperature = str_replace('$(function() {', '', $data_temperature);
            $data_temperature = substr($data_temperature, 0, strpos($data_temperature, 'var options'));

            preg_match("/var hot =(.*)\svar cold/", $data_temperature, $hots);
            $hot = trim($hots[1]);
            $hot = trim($hot, ';');

            preg_match("/var cold =(.*);/", $data_temperature, $colds);
            $cold = trim($colds[1]);
            $cold = trim($cold, ';');

            $begin_date = new DateTime();
            $hot_array = json_decode($hot);
            $cold_array = json_decode($cold, true);


            for ($i = 0; $i < $this->days; $i++) {
                $data_insert = array();
                if ($i == 0) {
                    $dd = $begin_date;
                } else {
                    $dd = $begin_date->modify('+1 day');
                }


                $data_insert['site_id'] = (int)$this->site_id;
                $data_insert['city_id'] = (int)$this->city_id;
                $data_insert['date'] = $dd->format('Y-m-d');
                $data_insert['min_temp'] = $cold_array[$i][1];
                $data_insert['max_temp'] = $hot_array[$i][1];

                $this->model->addWeatherRecord($data_insert);
            }

            $html->clear();
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