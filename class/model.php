<?php

/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 07.05.2016
 * Time: 12:56
 */
class  Model extends Helper
{
	private $db;
	private $file;

	public function __construct()
	{
		$this->db = new  MMySQLi (Config::get('hostname'), Config::get('username'),
			Config::get('password'), Config::get('database'));
		$this->file = new File(3600 * 3);
	}

	public function query($sql)
	{
		$data = array();

		$sql_id = md5($sql);
		$cache_result = $this->file->get($sql_id);
		if (!$cache_result) {

			$query = $this->db->query($sql);
			foreach ($query->rows as $result) {
				$data[] = $result;
			}
			$this->file->set($sql_id, $data);
		} else {
			$data = $cache_result;
		}
		return $data;
	}

	public function addWeatherRecord($data = array())
	{
		$sql = "INSERT INTO weather
          SET site_id = {$data['site_id']},
          `city_id` = {$data['city_id']},
          `date` = '{$data['date']}',
          `min_temp` = '{$data['min_temp']}',
          `max_temp` = '{$data['max_temp']}'";

		$this->db->query($sql);
	}

	public function getCityName($city_id)
	{
		$sql = "SELECT `name` FROM city WHERE id = {$city_id}";
		$query = $this->db->query($sql);
		return $query->row['name'];
	}

	public function getSites($search = false)
	{
		$data = array();
		if ($search) {
			$sql = "SELECT * FROM site WHERE `name` LIKE '%{$search}%' AND `status` = 1";
		} else {
			$sql = 'SELECT * FROM site WHERE `status` = 1';
		}

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$data[] = $result;
		}

		return $data;
	}

	public function getCities($search = false)
	{
		$data = array();
		if ($search) {
			$sql = "SELECT * FROM city WHERE `name` LIKE '%{$search}%' OR `name_iso` LIKE '%{$search}%'";
		} else {
			$sql = 'SELECT * FROM city';
		}

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$data[] = $result;
		}

		return $data;
	}

	public function getCookieSiteId()
	{
		//$this->var_dump($_COOKIE['site_id']);
		$site_id = (int)(isset($_COOKIE['site_id']) ? $_COOKIE['site_id'] : 1);
		if ($site_id < 1) {
			$site_id = 1;
		}
		return $site_id;
	}

	public function getCookieCityId()
	{
		$default_city = (int)Config::get('default_city');
		$city_id = (int)(isset($_COOKIE['city_id']) ? $_COOKIE['city_id'] : $default_city);
		if ($city_id <= 0) {
			$city_id = $default_city;
		}
		return $city_id;
	}

	public function userExists($email)
	{
		if (!is_int($email)) {
			$sql = "SELECT EXISTS(SELECT * FROM `user` WHERE
            email = '{$email}') as 'yes'";
		} else {
			$sql = "SELECT EXISTS(SELECT * FROM `user` WHERE
            user_id = '{$email}') as 'yes'";
		}

		$query = $this->db->query($sql);

		return (bool)$query->row['yes'];
	}

	public function addUser($data)
	{
		$pass = md5($data['pass']);
		$sql = "INSERT INTO `user`
          SET
          `pass` = '{$pass}',
          `email` = '{$data['email']}',
          `name` = '{$data['name']}'";

		$this->db->query($sql);
	}

	public function getUserByEmail($email)
	{
		$sql = "SELECT * FROM `user` WHERE email = '{$email}'";
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function getUserById($user_id)
	{
		$sql = "SELECT * FROM `user` WHERE user_id = '{$user_id}'";
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function setMessage($key, $message)
	{
		$_SESSION[$key] = $message;
	}

	public function getMessage($key)
	{
		if (isset($_SESSION[$key])) {
			$message = $_SESSION[$key];
		} else {
			$message = false;
		}
		return $message;
	}

	public function unsetMessage($key)
	{
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}

	public function getDataAnalyze()
	{
		$default_city = (int)Config::get('default_city');
		$city_id = (int)(isset($_COOKIE['city_id']) ? $_COOKIE['city_id'] : $default_city);
		if ($city_id <= 0) {
			$city_id = $default_city;
		}

		$days = (isset($_GET['days'])) ? ((int)$_GET['days']--) : 6;

		$begin_date = new DateTime();
		$start_date = new DateTime();
		$start_date_plus_day = new DateTime();
		$start_date_plus_day->modify('+1 day');

		$end_date = $begin_date->modify('-' . $days . ' day');


		$sql = "SELECT `site_id`, site.name, `city_id`, `date`, date(date_write) as `datew`,
                    avg(`min_temp`) as `min`, avg(`max_temp`) as `max`
                    FROM `weather`
                    JOIN `site`  ON (weather.site_id = site.id )
                    WHERE `city_id` = {$city_id}
                    AND DATE(`date`) = '{$start_date->format('Y-m-d')}'
                    AND (`date_write` BETWEEN '{$end_date->format('Y-m-d')}'
                      AND '{$start_date_plus_day->format('Y-m-d')}')
                    AND `status` = 1
                    GROUP BY   date(date_write),site_id
                    order by site_id, date_write";

		//echo $sql;

		$rez = array();

		$data = $this->query($sql);

		foreach ($data as $value) {
			$temp = array();
			$date = DateTime::createFromFormat('Y-m-d', $value['date']);
			$datew = DateTime::createFromFormat('Y-m-d', $value['datew']);

			$temp['datew'] = ($start_date->format('Y-m-d') != $datew->format('Y-m-d')) ?
				$datew->format('d.m.Y') : 'Сьогодні';
			$temp['date'] = $date->format('d.m.Y');
			$temp['name'] = $value['name'];
			$temp['min'] = round($value['min'], 2);
			$temp['max'] = round($value['max'], 2);

			$rez[] = $temp;
		}

		$rez = $this->group_assoc($rez, 'name');

		return $rez;
	}


	public function getWeatherAll($date)
	{
		$default_city = (int)Config::get('default_city');
		$city_id = (int)(isset($_COOKIE['city_id']) ? $_COOKIE['city_id'] : $default_city);
		if ($city_id <= 0) {
			$city_id = $default_city;
		}

		if ($date > 0) {
			$begin_date = new DateTime($date);
			$start_date = new DateTime($date);
		} else {
			$begin_date = new DateTime();
			$start_date = new DateTime();
		}


		//$minus_3_hour = new DateTime();

		$end_date = $begin_date->modify('+6 day');
		//$minus_3_hour = $minus_3_hour->modify('-3 hour');

		$sql = "SELECT `site_id`, site.name `city_id`, `date`,
                    avg(`min_temp`) as `min`, avg(`max_temp`) as `max`
                    FROM `weather`
                    JOIN `site`  ON (weather.site_id = site.id )
                    WHERE `city_id` = {$city_id}
                    AND DATE(`date_write`) = '{$start_date->format('Y-m-d')}'
                    AND (`date`
                      BETWEEN '{$start_date->format('Y-m-d')}' AND '{$end_date->format('Y-m-d')}')
                    AND `status` = 1
                    group by site_id, `date`
                    order by site_id, `date`";

		$rez = array();

		$data = $this->query($sql);


		$city_name = $this->getCityName($city_id);

		$categories = array();
		$series = array();
		$sites = $this->getSites();

		$forecasts = array();


		$data_all_temperature = array();

		foreach ($data as $value) {
			$temp = array();
			$date = DateTime::createFromFormat('Y-m-d', $value['date']);

			$temp['date'] = $date->format('d.m');
			$temp['min'] = $value['min'];
			$temp['max'] = $value['max'];

			$categories[] = $date->format('d.m.Y');
			$data_all_temperature[] = $temp;
		}

		$categories = array_unique($categories);

		foreach ($categories as $category) {
			$temp = array();
			$date = DateTime::createFromFormat('d.m.Y', $category);
			$day_now = $this->getDayUkr($date->format('w'));
			$temp['day'] = $day_now;
			$temp['day_date'] = $date->format('d.m');

			// температура
			$min = 0;
			$max = 0;
			$k = 0;
			foreach ($data_all_temperature as $value) {
				if ($value['date'] == $temp['day_date']) {
					$min += $value['min'];
					$max += $value['max'];
					$k++;
				}
			}
			$min /= $k;
			$max /= $k;

			$temp['min'] = round($min);
			$temp['max'] = round($max);

			$forecasts[] = $temp;
		}

		//$this->var_dump($forecasts);

		$series_max = array();
		// series on chart
		foreach ($sites as $site) {
			$temp = array();

			$temp['name'] = $site['name'];
			$temp['color'] = $site['color'];
			$temp['marker'] = array('symbol' => 'square');
			$temp['data'] = array();

			$temp_m = $temp;

			foreach ($data as $value) {
				if ($value['site_id'] == $site['id']) {
					$temp['data'] [] = round($value['min']);
					$temp_m['data'] [] = round($value['max']);
				}
			}

			$series_max [] = $temp_m;
			$series [] = $temp;
		}

		//  середнє значення
		$temp = array();

		$temp['name'] = 'Середнє';
		$temp['color'] = 'rgb(85, 191, 59)';
		$temp['marker'] = array('symbol' => 'diamond');
		$temp['data'] = array();

		$temp_m = $temp;

		foreach ($forecasts as $value) {
			$temp['data'] [] = round($value['min']);
			$temp_m['data'] [] = round($value['max']);
		}

		$series [] = $temp;
		$series_max [] = $temp_m;


		$rez['categories'] = $categories;
		$rez['city_name'] = $city_name;
		$rez['series'] = $series;
		$rez['series_max'] = $series_max;
		$rez['forecasts'] = $forecasts;

		return $rez;
	}

	public function getWeather()
	{

		$city_id = $this->getCookieCityId();
		$site_id = $this->getCookieSiteId();

		$begin_date = new DateTime();
		$start_date = new DateTime();
		$end_date = $begin_date->modify('+6 day');

		$sql = "SELECT `site_id`, site.name `city_id`, `date`,
                    avg(`min_temp`) as `min`, avg(`max_temp`) as `max`
                    FROM `weather`
                    JOIN `site`  ON (weather.site_id = site.id )
                    WHERE `city_id` = {$city_id}
                    AND DATE(`date_write`) = CURDATE()
                    AND `site_id` = {$site_id}
                    AND (`date` BETWEEN '{$start_date->format('Y-m-d')}' AND '{$end_date->format('Y-m-d')}')
                    group by site_id, `date`
                    order by site_id, `date`";

		//$this->var_dump($sql);
		$rez = array();

		$data = $this->query($sql);


		$city_name = $this->getCityName($city_id);

		$categories = array();
		$series = array();
		$sites = $this->getSites();

		$forecasts = array();


		$data_all_temperature = array();

		foreach ($data as $value) {
			$temp = array();
			$date = DateTime::createFromFormat('Y-m-d', $value['date']);

			$temp['date'] = $date->format('d.m');
			$temp['min'] = $value['min'];
			$temp['max'] = $value['max'];

			$categories[] = $date->format('d.m.Y');
			$data_all_temperature[] = $temp;
		}

		$categories = array_unique($categories);

		$series_max = array();
		// series on chart
		foreach ($sites as $site) {
			if ($site['id'] == $site_id) {
				$temp = array();

				$temp['name'] = $site['name'] . ' - min';
				$temp['color'] = $site['color'];
				$temp['marker'] = array('symbol' => 'square');
				$temp['data'] = array();

				$temp_m = $temp;

				$temp_m['name'] = $site['name'] . ' - max';
				$temp_m['color'] = $site['color'];
				$temp_m['marker'] = array('symbol' => 'square');

				foreach ($data as $value) {
					if ($value['site_id'] == $site['id']) {
						$temp['data'] [] = round($value['min']);
						$temp_m['data'] [] = round($value['max']);
					}
				}

				$series [] = $temp;
				$series [] = $temp_m;
			}
		}

		// заповнити прогноз
		$i = 0;
		foreach ($categories as $category) {
			$temp = array();
			$date = DateTime::createFromFormat('d.m.Y', $category);
			$day_now = $this->getDayUkr($date->format('w'));
			$temp['day'] = $day_now;
			$temp['day_date'] = $date->format('d.m');

			// температура
			$min = $series[0]['data'][$i];
			$max = $series[1]['data'][$i];

			$temp['min'] = round($min);
			$temp['max'] = round($max);

			$forecasts[] = $temp;
			$i++;
		}

		$rez['categories'] = $categories;
		$rez['city_name'] = $city_name;
		$rez['series'] = $series;
		$rez['forecasts'] = $forecasts;

		return $rez;
	}

	public function getSCities()
	{
		$sql = 'SELECT * FROM city';
		$data = $this->query($sql);
		$r = array();
		foreach ($data as $d) {
			$r[] = $d['name'];
			$r[] = $d['name_iso'];
		}
		return $r;
	}

	public function getSitesForSelect()
	{
		$data = $this->getSites();
		$rez = array();

		foreach ($data as $d) {
			$temp['value'] = $d['id'];
			if ($this->getCookieSiteId() == $d['id']) {
				$temp['selected'] = true;
			} else {
				$temp['selected'] = false;
			}

			$temp['text'] = $d['name'];
			$temp['imageSrc'] = $d['image_url'];
			$rez[] = $temp;
		}

		return $rez;
	}


}