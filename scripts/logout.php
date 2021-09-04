<?php
	session_start();
	if(isset($_SESSION['id'])) {
		unset($_SESSION['id']);
		unset($_SESSION['login']);
		unset($_SESSION['password']);
		header("Location: /");
	}
	else {
		header("Location: /");
	}