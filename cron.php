<?php
$start = microtime(true);
include(__DIR__ . '/class/autoloader.php');

new AutoLoader();
$model = new  Model();

$db = new  MMySQLi (Config::get('hostname'), Config::get('username'),
	Config::get('password'), Config::get('database'));

// sites
$cities = array();
$sql = 'SELECT * FROM city';
$query = $db->query($sql);
foreach ($query->rows as $result) {
	$cities[] = $result;
}


// sites
$sites = $model->getSites();

foreach ($sites as $site) {
	if ($site['name'] == 'YahooWeather')
		if (class_exists($site['name'])) {
			$site_class = new $site['name']();

			foreach ($cities as $city) {
				$site_class->buildQuery($city);
				$site_class->setSiteId($site['id']);
				$site_class->setCityId($city['id']);
				$site_class->addWeatherData();
				break;
			}
		}
}

echo "<br>Час виконання: " . round(microtime(true) - $start, 4) . ' с.';
//mail('tegosiv@gmail.com', 'cron', 'Час виконання: ' . round(microtime(true) - $start, 4) . ' с.');