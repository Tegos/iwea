<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 11:16
 */
class Interia extends Helper implements ISiteHelper
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
        $city_name = $city['name_pl'];
        $cid_pl = $city['cid_pl'];

        $this->url = 'https://pogoda.interia.pl/';
        $this->url .= "prognoza-dlugoterminowa-{$city_name},cId,{$cid_pl}";
    }


    public function addWeatherData()
    {
        $content = $this->get_web_page($this->url);
        $html = simple_html_dom::str_get_html($content);

        $lists = $html->find('.weather-forecast-longterm-list', 0);

        $lies = $lists->children();

        $i = 0;
        foreach ($lies as $li) {
            if ($li->tag == 'li') {
                $data_insert = array();

                $date = trim($li->find('.date', 0)->plaintext);
                $date .= '.' . date('Y');

                $DateTime = DateTime::createFromFormat('d.m.Y', $date);

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