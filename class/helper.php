<?php

class Helper
{
	function check($msg)
	{

		if (is_array($msg)) {
			foreach ($msg as $key => $val) {
				$msg[$key] = check($val);
			}
		} else {
			$msg = htmlspecialchars($msg);
			$search = array('|', '\'', '$', '\\', '^', '%', '`', "\0", "\x00", "\x1A", chr(226) . chr(128) . chr(174));
			$replace = array('&#124;', '&#39;', '&#36;', '&#92;', '&#94;', '&#37;', '&#96;', '', '', '', '');
			$msg = str_replace($search, $replace, $msg);
			$msg = stripslashes(trim($msg));
		}
		return $msg;
	}

	function convert($size)
	{
		$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
		return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
	}

	public function var_dump($data = array())
	{
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}

	public function strip_tags_content($text, $tags = '', $invert = FALSE)
	{

		preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
		$tags = array_unique($tags[1]);

		if (is_array($tags) AND count($tags) > 0) {
			if ($invert == FALSE) {
				return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
			} else {
				return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
			}
		} elseif ($invert == FALSE) {
			return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
		}
		return $text;
	}

	public function cleanHtml($str, $space = false)
	{
		$s = preg_replace("/&#?[a-z0-9]{2,8};/i", " ", $str);
		$s = trim($s);
		return $s;
	}

	public function getUserAgent($bot = false)
	{
		if (!$bot)
			return $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
		return $user_agent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

	}

	public function get_web_page($url)
	{
		$user_agent = $this->getUserAgent();

		$cookie = realpath('') . '/data/cookie.txt';
		$cert = realpath('') . '/data/cacert.pem';


		$options = array(

			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POST => false,
			CURLOPT_USERAGENT => $user_agent,
			CURLOPT_COOKIESESSION => true,
			CURLOPT_COOKIEJAR => $cookie,
			CURLOPT_COOKIEFILE => $cookie,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_AUTOREFERER => true,
			CURLOPT_CONNECTTIMEOUT => 3,
			CURLOPT_TIMEOUT => 5,
			CURLOPT_MAXREDIRS => 5,
			CURLOPT_CAINFO => $cert
		);

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		$header = curl_getinfo($ch);
		curl_close($ch);

		//$header['errno'] = $err;
		//$header['errmsg'] = $errmsg;
		$header['content'] = $content;
		return $content;
	}

	public function getWebPageProxy($url = 'http://ifconfig.me/ip', $k = 0)
	{
		$proxies = $this
			->get_web_page('http://gimmeproxy.com/api/getProxy?supportsHttps=true&');
		$proxies = json_decode($proxies, true);


		if (!key_exists('ipPort', $proxies)) {
			$ipPort = $this->getIpPort();
		} else {
			$ipPort = $proxies['ipPort'];
		}

		if (!$this->isValidProxy($ipPort)) {
			return 0;
		}

		//$this->var_dump($ipPort);
		//$this->var_dump($proxies);

		$user_agent = $this->getUserAgent();

		// Create a stream
		$opts = [
			'http' => array(
				'method' => "GET",
				'header' => "Accept-language: *\r\n" .
					//"Cookie: foo=bar\r\n",
					"User-Agent: {$user_agent}\r\n",
				//'proxy' => '180.234.223.92:8080',
				'proxy' => $ipPort,
			),
			'ssl' => array(
				'SNI_enabled' => false
			)
		];

		$context = stream_context_create($opts);

		// Open the file using the HTTP headers set above
		//$url = urlencode($url);
		$file = @file_get_contents($url, false, $context);

		if ($file) {
			return $file;
		} else {
			if ($k < 5) {
				$k++;
				$this->getWebPageProxy($url, $k);
			}

		}
		return 0;
	}

	public function isValidProxy($proxy)
	{
		$expl = explode(':', $proxy);
		if (empty($expl[0]) || empty($expl[1])) {
			return false;
		}
		return true;
	}

	public function getIpPort()
	{
		$proxies = [
			'freeproxy' =>
				[
					'url' => 'http://www.freeproxy-list.ru/api/proxy?anonymity=false&count=1&token=demo',
					'ip_field' => null,
					'json' => false
				],
			'getproxylist' =>
				[
					'url' => 'https://api.getproxylist.com/proxy?allowsHttps=1',
					'ip_field' => 'ip',
					'port' => 'port',
					'json' => true
				]
		];

		$rand = (float)rand() / (float)getrandmax();
		if ($rand < 0.7)
			$result = 'freeproxy';
		else
			$result = 'getproxylist';

		//$proxy = $proxies[array_rand($proxies, 1)];
		$proxy = $proxies[$result];

		$response = $this->get_web_page($proxy['url']);

		if ($proxy['json']) {
			$response = json_decode($response, true);

			$ipPort = "{$response[$proxy['ip']]}:{$response[$proxy['port']]}";
		} else {
			$ipPort = $response;
		}

		return $ipPort;
	}

	public function getDayUkr($w)
	{
		$days = array(
			'Неділя', 'Понеділок', 'Вівторок', 'Середа',
			'Четвер', 'П`ятниця', 'Субота'
		);

		$name_day = $days[$w];
		return $name_day;
	}

	public function getMonthUkr($month, $genitive = false, $low = false)
	{
		$trans = array(
			"Jan" => "Січень",
			"Feb" => "Лютий",
			"Mar" => "Березень",
			"Apr" => "Квітень",
			"May" => "Травень",
			"Jun" => "Червень",
			"Jul" => "Липень",
			"Aug" => "Серпень",
			"Sep" => "Вересень",
			"Oct" => "Жовтень",
			"Nov" => "Листопад",
			"Dec" => "Грудень"
		);

		$result = strtr($month, $trans);
		if ($low)
			$result = mb_strtolower($result);

		return $result;
	}

	public function group_assoc($array, $key)
	{
		$return = array();
		foreach ($array as $v) {
			$return[$v[$key]][] = $v;
		}
		return $return;
	}

	public function base64_url_encode($input)
	{
		return strtr(base64_encode($input), '+/=', '-_~');
	}

	public function base64_url_decode($input)
	{
		return base64_decode(strtr($input, '-_~', '+/='));
	}

	public function dateTimesToDays($start, $end)
	{
		$difference = $end->diff($start);
		$days = (int)$difference->format('%a');
		return $days;
	}

	public function getTitlePage($date = 0)
	{
		$title = 'Погода сьогодні';
		if ($date == 0 || !($date instanceof DateTime)) {
			$date = new DateTime();
			return $title;
		}

		$now_month = $this->getMonthUkr($date->format('M'));
		$now_month_d = $date->format('d');
		$year_weather = $date->format('Y');

		$title = "Погода на $now_month $now_month_d, $year_weather";

		return $title;

	}

	public function getNextSite($sites)
	{
		$f = __DIR__ . Config::get('sync_file');
		$site = file_get_contents($f);

		if (in_array($site, $sites)) {
			return trim($site);
		}
		return $sites[0];
	}

	public function setNextSite($site)
	{
		$f = __DIR__ . Config::get('sync_file');
		$r = @file_put_contents($f, $site);
		return $r;
	}

	public function getStateRun()
	{
		$f = __DIR__ . Config::get('state_file');
		$site = file_get_contents($f);

		return trim($site);

	}

	public function setStateRun($state)
	{
		$f = __DIR__ . Config::get('state_file');
		$r = @file_put_contents($f, $state);
		return $r;
	}

	public function getPolishDays($small = false)
	{
		if ($small) {
			return array('Nd', 'Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'Sb');
		}

		return array(
			'Niedziela',
			'Poniedziałek',
			'Wtorek',
			'Środa', 'Czwartek',
			'Piątek',
			'Sobota');
	}

	public function getIndexOfPolishDay($day)
	{
		$days = $this->getPolishDays();
		for ($i = 0; $i < count($days); $i++) {
			if (strcasecmp($days[$i], $day) == 0) {
				return $i;
			}
		}
		return -1;
	}

	public function getToday()
	{
		$today = new DateTime('now',
			new DateTimeZone(
				Config::get('timeZone')
			)
		);
		return $today;
	}


}


