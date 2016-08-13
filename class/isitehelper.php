<?php
/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 0:33
 */

interface ISiteHelper {


    public function buildQuery($city);
    public function addWeatherData();
    public function setSiteId($site_id);
    public function setCityId($city_id);
}