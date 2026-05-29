<?php 

date_default_timezone_set('Asia/Jakarta');

spl_autoload_register(function($class) {
	require_once 'core/'.$class.'.php';	
});