<?php
use StdLib\Application;

function pre($str)
{
	echo '<pre>';
	print_r($str);
	echo '</pre>';
}

function pred($str)
{
	pre($str);
	die();
}

function always_set($array, $key, $default = false)
{
	return isset($array[$key]) ? $array[$key] : $default;
}

function config($name)
{
	$config = Application::getInstance()->getServiceManager()->get('config');
	return $config[$name];
}