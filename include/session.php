<?php
error_reporting(E_ALL & ~E_NOTICE);
header("Content-type: text/html; charset=utf-8");
session_start();
if (isset($_GET['e'])) {
	$logout = trim($_GET['e']);
	if ($logout == "3"){
		session_destroy();
		header( "Location: " . dirname(dirname($_SERVER['REQUEST_URI'])) . "../index.php?loggedout=yes" );
		exit;
		}
	}