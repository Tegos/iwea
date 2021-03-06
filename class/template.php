<?php
/**
 * Created by PhpStorm.
 * User: IBAH
 * Date: 08.05.2016
 * Time: 15:35
 */
use zz\Html\HTMLMinify;

class Template
{

	private $vars = array();

	public function __construct()
	{
	}

	public function __get($name)
	{
		return $this->vars[$name];
	}

	public function __set($name, $value)
	{
		if ($name == 'view_template_file') {
			throw new Exception("Cannot bind variable named 'view_template_file'");
		}
		$this->vars[$name] = $value;
	}

	public function render($view_template_file)
	{
		//var_dump($view_template_file);
		if (array_key_exists('view_template_file', $this->vars)) {
			throw new Exception("Cannot bind variable called 'view_template_file'");
		}
		extract($this->vars);


		if (isset($view_template_file)) {

			ob_start();
			include(HOME . 'template/' . $view_template_file . '.tpl');
			$string = ob_get_clean();

			$option = array('optimizationLevel' => HTMLMinify::OPTIMIZATION_ADVANCED);
			$min_html = HTMLMinify::minify($string, $option);

			return $min_html;
		}
		return '';
	}
}