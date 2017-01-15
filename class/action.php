<?php

/**
 * Created by PhpStorm.
 * User: Personal computer
 * Date: 22.05.2016
 * Time: 20:32
 */
class Action extends Helper
{
	private $model;
	private $file;
	private $arg;

	public function __construct($model, $arg = array())
	{
		$this->model = $model;
		$this->arg = $arg;
		$this->file = new File(3600 * 3);
	}

	public function header(&$view)
	{
		$weather = $this->model->getWeather();
		$city_name = $weather['city_name'];

		$keywords = array(
			'iwea',
			'огляд погоди',
			'порівння погоди',
			'погода',
			'прогноз погоди',
			'справочнi данi',
			'аналіз погоди',
			'погода з різних сайтів',
			'погода з різних джерел',
			'погода на 10 днів',
			'графіки',
			'графіки температур',
			'SINOPTIK',
			'METEOPROG',
			'Interia',
			'TheDarkSkyCompany',
			'OpenWeatherMap',
			'WorldWeatherOnline',
			'SinoptikUa',
			'AerisWeather',
			'Список погодних джерел',
			'Середня температура',
			'Середня температура з різних сайтів',
			'Класифікація даних сайтів прогнозу погоди',
			'Класифікація прогнозу погоди',
			'Класифікація погоди',
			'Матриця відстаней між графіками температур',
			'Матриця відстаней',
			'Матриця',
			'Групи',
			'Групи погодних сайтів',
			'Порівняльний аналіз даних прогнозу погоди різних сайтів',
			'Порівняльний аналіз даних прогнозу',
			'Порівняльний аналіз прогнозу погоди',
			'Інтервали',
			'Похибка',
			'Коефіцієнти похибки сайтів',
			'Точність погоди',
			'Різниця температур між різними джерелами',
			'Різниця температур',
		);

		$description = 'iWea-порівнюй, аналізуй погоду і отримуй достовірний результат.';
		$generation_description = " Погода у {$city_name} на 7 днів,
        детальний прогноз погоди на 7 днів у {$city_name}.";

		$description .= $generation_description;

		$keywords[] = $city_name;
		$keywords = array_map('mb_strtolower', $keywords);

		$view->keywords = implode(',', $keywords);

		$view->description = $description;


		$view->user = $this->isUser();
	}

	public function home(&$view)
	{
		$s = '\'now\', new DateTimeZone(\'Europe/Kiev\')';
		$weather = $this->model->getWeather();
		$date_now = $this->getToday();
		$day_now = $this->getDayUkr($date_now->format('w'));
		$now_month = $this->getMonthUkr($date_now->format('M'));
		$now_month_d = $date_now->format('d');

		$view->is_home = true;
		$view->categories = json_encode($weather['categories']);
		$view->series = json_encode($weather['series']);
		$view->city_name = $weather['city_name'];
		$view->title = 'iWEA - Веб-застосування для порівняння прогнозу погоди';
		$view->day_now = $day_now;
		$view->forecasts = $weather['forecasts'];
		$view->now_month = $now_month;
		$view->now_month_d = $now_month_d;

		$view->site_id = $this->model->getCookieSiteId();
		$view->canonical = Config::get('domen');

		$view->chart = $view->render('chart');

	}

	public function search_page(&$view)
	{
		$search = isset($_GET['search']) ? $_GET['search'] : '';
		$results = $this->model->getCities($search);

		$view->title = 'iWEA — Пошук';
		$view->results = $results;
		$view->canonical = Config::get('domen');

	}

	public function not_found(&$view)
	{

		header('HTTP/1.0 404 Not Found', true, 404);
		$view->title = 'iWEA — 404';
		$view->canonical = Config::get('domen') . '/not_found';
	}

	public function info(&$view)
	{
		$view->title = 'Список джерел';
		$view->sites = $this->model->getSites();
		$view->canonical = Config::get('domen') . '/info';
	}

	public function sitemap()
	{
		$sitemap = $this->file->get('sitemap');
		$domen = Config::get('domen');


		$pages = array('', 'info', 'all', 'analytics', 'search');

		$start = new DateTime(Config::get('start_date'));
		$end = new DateTime();
		//
		$days = $this->dateTimesToDays($start, $end);

		if (!$sitemap) {

			$output = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

			// pages
			foreach ($pages as $page) {
				$url = "$domen/$page";

				$output .= '<url>';
				$output .= '<loc>' . $url . '</loc>';
				$output .= '<changefreq>daily</changefreq>';
				$output .= '<priority>0.8</priority>';
				$output .= '</url>';
			}

			//$this->var_dump($days);
			//exit();

			// for past dates
			$date_start = new DateTime();
			$date_start->modify("-{$days} days");

			for ($i = 0; $i < $days; $i++) {
				$f = $date_start->format('d-m-Y');
				$array = array('d' => $f);
				$param = http_build_query($array);
				$param = $this->base64_url_encode($param);
				$url = "$domen/all/$param";

				$output .= '<url>';
				$output .= '<loc>' . $url . '</loc>';
				$output .= '<changefreq>daily</changefreq>';
				$output .= '<priority>0.7</priority>';
				$output .= '</url>';

				$date_start->modify('+ 1day');
			}


			$output .= '</urlset>';

			$this->file->set('sitemap', $output);
			$sitemap = $output;
		}

		header('Content-Type: application/xml');
		echo $sitemap;

		exit();
	}

	public function auth_reg(&$view)
	{
		$view->title = 'iWEA — Авторизація';
		if ($this->model->getMessage('reg_success')) {
			$view->result = 'Вітаємо! Ви успішно зареєструвались.';
		} else {
			$view->result = false;
		}

	}

	public function reg(&$view)
	{

		if ($this->model->getMessage('error')) {
			$view->error = unserialize($this->model->getMessage('error'));
			$this->model->unsetMessage('error');
		} else {
			$view->error = false;
		}

		$view->title = 'iWEA — реєстрація';
	}


	public function all(&$view)
	{
		error_reporting(0);
		$start_date = new DateTime(Config::get('start_date'));
		$date_now = $this->getToday();
		$today = true;

		$param_d = '';
		if (isset($this->arg)) {
			try {
				$get_string = $this->arg;
				if (!empty($get_string)) {
					$param_d = '/' . $get_string;
					$get_string = $this->base64_url_decode($get_string);
					parse_str($get_string, $get_array);
					$this->arg = $get_array;
				}

			} catch (Exception $e) {
				$this->arg = array();
			}
		}

		$canonical = Config::get('domen') . "/all{$param_d}";

		if (count($this->arg) > 0) {
			if (isset($this->arg['d'])) {
				$d = $this->arg['d'];
				try {
					$date_now = new DateTime($d);
					$today = false;
				} catch (Exception $e) {

				}
			}
		}

		$date_format = $date_now->format('Y-m-d');

		$weather = $this->model->getWeatherAll($date_format);

		//$this->var_dump($date_now->format('d m Y'));

		$day_now = $this->getDayUkr($date_now->format('w'));
		$now_month = $this->getMonthUkr($date_now->format('M'));
		$now_month_d = $date_now->format('d');
		$year_weather = $date_now->format('Y');

		$view->canonical = $canonical;
		$view->categories = json_encode($weather['categories']);
		$view->series = json_encode($weather['series']);
		$view->series_max = json_encode($weather['series_max']);
		$view->city_name = $weather['city_name'];
		$view->title = 'Погода сьогодні';
		$view->day_now = $day_now;
		$view->forecasts = $weather['forecasts'];
		$view->now_month = $now_month;
		$view->now_month_d = $now_month_d;

		$page_title = $this->getTitlePage();
		if (!$today) {
			$page_title = $this->getTitlePage($date_now);
			$view->title = $page_title;

		}


		$today_day = $this->getToday();
		$today_format = $today_day->format('Y-m-d');

		if ($date_format != $today_format) {
			$prev_date = clone $date_now;
			$next_date = clone $date_now;

			$prev_date->modify('-1 day');
			$next_date->modify('+1 day');


			$prev_date_format = $prev_date->format('d-m-Y');
			$next_date_format = $next_date->format('d-m-Y');


			//prev

			if ($start_date < $prev_date) {

				$array = array('d' => $prev_date_format);
				$param = http_build_query($array);
				$param = $this->base64_url_encode($param);
				$url_prev = "/all/$param";
				$view->url_prev = $url_prev;
				$view->title_prev = $this->getTitlePage($prev_date);
			}

			// next
			$array = array('d' => $next_date_format);
			$param = http_build_query($array);
			$param = $this->base64_url_encode($param);
			$url_next = "/all/$param";
			$view->url_next = $url_next;
			$view->title_next = $this->getTitlePage($next_date);

		} else { // today
			$today_day->modify('-1 day');
			$prev_date_format = $today_day->format('d-m-Y');

			//prev
			$array = array('d' => $prev_date_format);
			$param = http_build_query($array);
			$param = $this->base64_url_encode($param);
			$url_prev = "/all/$param";
			$view->url_prev = $url_prev;
			$view->title_prev = $this->getTitlePage($today_day);
		}


		$view->page_title = $page_title;
		$view->is_today = $today;


		$view->site_id = $this->model->getCookieSiteId();


		$view->chart = $view->render('chart-all');

	}

	public function analytics(&$view)
	{
		$weather = $this->model->getWeatherAll(0);


		$view->categories = json_encode($weather['categories']);
		$view->series = json_encode($weather['series']);
		$view->series_max = json_encode($weather['series_max']);
		$view->city_name = $weather['city_name'];
		$view->title = 'Аналітика';

		$view->canonical = Config::get('domen') . '/analytics';
		$view->sites = $this->model->getSites();


		unset ($weather['series'][count($weather['series']) - 1]);
		$series = json_encode($weather['series']);
		$view->series = $series;

		unset ($weather['series_max'][count($weather['series_max']) - 1]);
		$series_max = json_encode($weather['series_max']);
		$view->series_max = $series_max;


		$view->site_id = $this->model->getCookieSiteId();


		$view->chart_diff = $view->render('chart-diff');

	}

	public function auth()
	{
		$email = $_POST['email'];
		$pass = $_POST['pass'];

		$error = array();

		if (strlen($email) > 5 && strlen($pass) > 3) {
			if ($this->model->userExists($email)) {

				$user = $this->model->getUserByEmail($email);

				// авторизований
				if ($user['pass'] == md5($pass)) {
					$error['status'] = false;
					$this->model->setMessage('auth_success', true);

					setcookie('pass', $user['user_id']);
					setcookie('time', intnum::fromString($user['pass']));
					header("Location: /");

				} else {
					$error['status'] = true;
					$error['message'] = 'Неправильний пароль.';
				}

			} else {
				$error['status'] = true;
				$error['message'] = 'Нема такого користувача.';
			}
		} else {
			$error['status'] = true;
			$error['message'] = 'Перевірте введені дані.';
		}

		if ($error['status']) {
			$this->model->
			setMessage('error', serialize($error));
			header("Location: /?action=reg");
		} else {
			header("Location: /");
		}
	}

	public function registration()
	{
		$email = $_POST['email'];
		$pass = $_POST['pass'];
		$name = $_POST['name'];

		$error = array();

		if (strlen($email) > 5 && strlen($pass) > 3 && strlen($name) > 2) {
			if (!$this->model->userExists($email)) {
				$this->model->addUser(
					array(
						'email' => $email,
						'pass' => $pass,
						'name' => $name
					)
				);
				$error['status'] = false;
				$this->model->setMessage('reg_success', true);
			} else {
				$error['status'] = true;
				$error['message'] = 'Такий користувач вже існує.';
			}
		} else {
			$error['status'] = true;
			$error['message'] = 'Перевірте введені дані.';
		}

		if ($error['status']) {
			$this->model->
			setMessage('error', serialize($error));
			header("Location: /?action=reg");
		} else {
			header("Location: /?action=auth_reg");
		}
	}

	public function isUser()
	{
		$user_id = @$_COOKIE['pass'];
		$user_pass = intnum::fromNumber(@$_COOKIE['time']);
		$user = $this->model->getUserById($user_id);

		if ($user) {
			if ($user['pass'] == $user_pass) {
				return $user;
			}
		}
		return false;
	}

}