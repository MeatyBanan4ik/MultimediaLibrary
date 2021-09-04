<?php 
session_start();
require_once "../add/db.php";
if (isset($_POST['login'])) {
	//Есть пост запрос
	$errors = array();
	if(filter_var($_POST['login'], FILTER_VALIDATE_EMAIL)) {
		//ищем почту
		$user = R::findOne('clients', "email = ?", [trim(strtolower($_POST['login']))]);
	}
	else {
		//ищем логин
		$user = R::findOne('clients', "login = ?", [trim(strtolower($_POST['login']))]);
	}
	if($user) {
		//нашли пользователя
		$user->rec_t = date("Y-m-d H:i:s");
		if($user->email_verified == false){
				$user->email_verified = 0;
			}
		R::store($user);
		$hash = md5($user->hash.$user->rec_t);
		$subject = "=?utf-8?B?".base64_encode("Восстановление пароля")."?=";
		$headers = "From: $email\r\nContent-type: text/html;charset=utf-8\r\n";
		$link = "http://" . $_SERVER['HTTP_HOST'] ."/remember.php?l=". base64_encode($user->login) . "&h=" . $hash;
		$message = " <a href=$link class=\"btn btn-success\">Восстановить пароль</a><br/>Или по ссылке: $link";
		mail(trim($user->email), $subject, $message, $headers);
		header("Location: /");
	}
	else {
		//не нашли пользователя
		$errors[] = "Пользователя с ". (filter_var($_POST['login'], FILTER_VALIDATE_EMAIL) ? "такой почтой" : "таким логином") . " не существует";
	}
	if(!empty($errors)) {
		//если есть ошибки
		$_SESSION['errors_rm'] = $errors;
		header("Location: /remember.php");
	}
}
elseif (isset($_POST['new_password'])) {
	//новый пароль
	if(strpos((trim($_POST['new_password'])), 0x20) !== false) {
				$errors[] = 'Пароль не должен содержать пробелов';
			}
	if(strlen($_POST['new_password']) < 8) {
				$errors[] = 'Пароль должен быть не меньше 8 символов';
			}
	if(preg_match("/[A-Z]/", $_POST['new_password']) == 0) {
				$errors[] = 'Пароль должен иметь хотя бы 1 букву верхнего регистра';
			}
	if(preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $_POST['new_password']) == 0) {
				$errors[] = 'Пароль должен иметь хотя бы 1 спецсимвол';
			}
	if(preg_match("/[a-z]/", $_POST['new_password']) == 0) {
				$errors[] = 'Пароль должен иметь хотя бы 1 букву нижнего регистра';
			}
	if(preg_match("/[0-9]/", $_POST['new_password']) == 0) {
				$errors[] = 'Пароль должен иметь хотя бы 1 цифру';
			}
	if($_POST['new_password'] != $_POST['new_password2']) {
				$errors[] = 'Пароли не совпадают';
			}
	if(!empty($errors)){
		//ошибки
		$_SESSION['errors_rm'] = $errors;
		header("Location: /remember.php?l=" . $_GET['l']. "&h=" . $_GET['h']);
	}
	else {
		//нет ошибок
		$user = R::findOne('clients', "login = ?", [mb_convert_encoding(base64_decode($_GET['l']), "UTF-8")]);
		if($user){
			//меняем пароль
			/*if((strtotime(date("H:i:s d.m.Y")) - strtotime($user->rec_t)) > 24) {
				//время действие ссылки вышло
			}
			else {*/
			//еще не прошло 24 часа с момента запроса восстановления пароля
			$user->password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
			$user->rec_t = date("Y-m-d H:i:s");
			if($user->email_verified == false){
				$user->email_verified = 0;
			}
			R::selectDatabase('postgres');
			R::exec('ALTER ROLE "'. $user->login .'" WITH PASSWORD \''. $_POST['new_password'] .'\';');
			R::store($user);
			R::selectDatabase('non_auth');
			$hash = md5($user->hash.$user->rec_t);
			$subject = "=?utf-8?B?".base64_encode("Пароль успешно изменен")."?=";
			$headers = "From: CourseWork <meatybanana11@gmail.com>\r\nContent-type: text/html;charset=utf-8\r\n";
			$link = "http://" . $_SERVER['HTTP_HOST'] ."/remember.php?l=". base64_encode($user->login) . "&h=" . $hash;
			$message = "Пароль успешно изменен<br/>Если это были не вы нажмите на кнопку <a href=$link class=\"btn btn-success\">Восстановить пароль</a> или перейдите по ссылке: $link";
			mail(trim($user->email), $subject, $message, $headers);
			header("Location: /");
			$_SESSION['id'] = $user->id;
			//}
		}
		else {
			//вдруг такое случиться, что пользователя с таким логином не существует
			$_SESSION['errors_rm'] = ["Пользователя с логином " . mb_convert_encoding(base64_decode($_GET['l']), "UTF-8") . " не существует"];
			header("Location: /remember.php");
		}
	}
}
else {
	//Пользователь случайно попал сюда
	header("Location: /");
}