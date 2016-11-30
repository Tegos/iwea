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
		$weather = $this->model->getWeather();
		$date_now = new DateTime();
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

	public function info(&$view)
	{
		$view->title = 'iWEA — Список джерел';
		$view->sites = $this->model->getSites();
		$view->canonical = Config::get('domen') . '/info';
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
		$weather = $this->model->getWeatherAll();
		$date_now = new DateTime();
		$today = true;

		if (isset($this->arg)) {
			try {
				$get_string = $this->arg;
				if (!empty($get_string)) {
					$get_string = $this->base64_url_decode($get_string);
					parse_str($get_string, $get_array);
					$this->arg = $get_array;
				}

			} catch (Exception $e) {
				$this->arg = array();
			}
		}

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

		//$this->var_dump($date_now->format('d m Y'));

		$day_now = $this->getDayUkr($date_now->format('w'));
		$now_month = $this->getMonthUkr($date_now->format('M'));
		$now_month_d = $date_now->format('d');

		$view->canonical = Config::get('domen') . '/all';
		$view->categories = json_encode($weather['categories']);
		$view->series = json_encode($weather['series']);
		$view->series_max = json_encode($weather['series_max']);
		$view->city_name = $weather['city_name'];
		$view->title = 'iWEA — Погода сьогодні';
		$view->day_now = $day_now;
		$view->forecasts = $weather['forecasts'];
		$view->now_month = $now_month;
		$view->now_month_d = $now_month_d;

		$page_title = 'Погода сьогодні';
		if (!$today) {
			$page_title = "Погода на $now_month, $now_month_d";

		} else {
			$today_day = new DateTime();
			$today_day->modify('-1 day');
			$f = $today_day->format('d-m-Y');
			$array = array('d' => $f);
			$param = http_build_query($array);
			$param = $this->base64_url_encode($param);
			$url_yesterday = "/all/$param";
			$view->url_yesterday = $url_yesterday;
		}


		$view->page_title = $page_title;
		$view->is_today = $today;


		$view->site_id = $this->model->getCookieSiteId();


		$view->chart = $view->render('chart-all');

	}

	public function analytics(&$view)
	{
		$weather = $this->model->getWeatherAll();


		$view->categories = json_encode($weather['categories']);
		$view->series = json_encode($weather['series']);
		$view->series_max = json_encode($weather['series_max']);
		$view->city_name = $weather['city_name'];
		$view->title = 'iWEA — аналітика';

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