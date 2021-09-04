<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$user = R::findOne('clients', "id = ?", [$_SESSION['id']]);
		$subject = "=?utf-8?B?".base64_encode("Подтверждение почты")."?=";
		$headers = "From: $email\r\nContent-type: text/html;charset=utf-8\r\n";
		$link = "http://" . $_SERVER['HTTP_HOST'] ."/scripts/verified.php?l=". base64_encode($user->login) . "&h=" . $user->hash;
		$message = " <a href=$link class=\"btn btn-success\">Подтвердить почту</a><br/>Или по ссылке: $link";
		mail($user->email, $subject, $message, $headers);
		header('Location: /settings.php');
	}
	else {
		header('Location: /');
	}