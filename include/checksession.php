<?php
//check to make sure the session variable is registered
if(isset($_SESSION['user_id'])){
	$user_name = $_SESSION['user_name'];
	$user_id = $_SESSION['user_id'];
	$user_level = $_SESSION['user_level'];
	$user_nacl=$_SESSION['user_nacl'];
	}
	else{
		
		$href="../../../index.php";
	header('Location: '.$href);
	exit;
	}
?>