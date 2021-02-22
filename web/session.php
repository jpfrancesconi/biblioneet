<?php
session_start();
if(!isset($_SESSION['user'])) {
	$_SESSION['user'] = [
		'uid' => 2,
		'user' => 'pepes',
	];
}

var_dump($_SESSION);
