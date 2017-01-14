<?php
include(__DIR__ . '/class/autoloader.php');
include(__DIR__ . '/vendor/autoload.php');

if ($_SERVER['QUERY_STRING'] != '' && $_SERVER['QUERY_STRING'] != '/' && !isset($_GET['action']))
    header("Location: /", true, 302);

new AutoLoader();
new Controller();