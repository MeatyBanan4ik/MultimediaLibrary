<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		if(isset($_POST['up'])){
			R::exec('UPDATE clients SET balance = balance + ? WHERE id = ?', [rand(1, 100), $_SESSION['id']]);
		}
		elseif (isset($_POST['down'])) {
			R::exec('UPDATE clients SET balance = 0 WHERE id = ?', [$_SESSION['id']]);
		}
		header('Location: /settings.php?type=balance');
		exit();
	}
	else {
		header('Location: /');
		exit();
	}