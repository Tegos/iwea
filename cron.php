<?php
$start = microtime(true);
include(__DIR__ . '/class/autoloader.php');

new AutoLoader();
$model = new  Model();
$helper = new  Helper();
$log = new PHPLogger(__DIR__ . "/data/logs");
$tag = "CRON";


$log->i($tag, '---------------------');
$log->i($tag, "Cron start");

$state = $helper->getStateRun();

if ($state != 'run') {
	$helper->setStateRun('run');
	try {
		// cities
		$cities = $model->getCities();

		// sites
		$sites = $model->getSites();

		$site_arr = array();
		foreach ($sites as $site) {
			$site_arr[] = $site['name'];
		}

		foreach ($sites as $site) {
			$next_site = $helper->getNextSite($site_arr);

			if ($site['name'] == $next_site) {

				$site_id = $site['id'];
				if (class_exists($site['name'])) {
					$log->i($tag, "Site: {$next_site}");
					$site_class = new $site['name']();

					foreach ($cities as $city) {
						$site_class->buildQuery($city);
						$site_class->setSiteId($site['id']);
						$site_class->setCityId($city['id']);
						$site_class->addWeatherData();
					}
				}

				$site_id++;
				$next_site = $model->getSite($site_id);
				while ($next_site && !$next_site['status']) {
					$site_id++;
					$next_site = $model->getSite($site_id);
				}

				$helper->setNextSite(@$next_site['name']);
				$helper->setStateRun('');
				break;
			}

		}
	} catch (Exception $e) {
		$helper->setStateRun('');
		$log->e($tag, $e->getMessage());
	}
} else {
	$log->w($tag, 'Запущений інший крон');
}

$log->i($tag, 'Час виконання: ' . round(microtime(true) - $start, 4) . ' с.');