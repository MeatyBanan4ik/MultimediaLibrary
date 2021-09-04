<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id']) AND !empty($_POST['email'])) {
		require_once "../add/db.php";
		$check = R::findOne('clients', 'email = ?', [trim(strtolower($_POST['email']))]);
		if(empty($check))	{
			$user = R::findOne('clients', "id = ?", [$_SESSION['id']]);
			$subject = "=?utf-8?B?".base64_encode("Подтверждение почты")."?=";
			$headers = "From: $email\r\nContent-type: text/html;charset=utf-8\r\n";
			$link = "http://" . $_SERVER['HTTP_HOST'] ."/scripts/verified.php?l=". base64_encode($user->login) . "&h=" . $user->hash;
			$message = " <a href=$link class=\"btn btn-success\">Подтвердить почту</a><br/>Или по ссылке: $link";
			$user->email_verified = 0;
			$user->email = trim(strtolower($_POST['email']));
			R::store($user);
			mail(trim($_POST['email']), $subject, $message, $headers);
		}
		else {
			$_SESSION['errors_settings'] = ['Пользователь с такой почтой уже зарегистрирован'];
		}
		header('Location: /settings.php');
	}
	else {
		header('Location: /');
	}