<?php
	session_start();
	require_once "../add/db.php";
	if(isset($_POST['login'])) {
			//Нажал на кнопку
			$errors = array();
			$errors_n = array();
			$login = R::findOne('clients', 'login = ?', [trim(strtolower($_POST['login']))]);
			$email = R::findOne('clients', 'email = ?', [trim(strtolower($_POST['email']))]);
			if($login) {
				$errors_n[] = 1;
				$errors[] = 'Такой логин уже используется';
			}
			if($email) {
				$errors_n[] = 2;
				$errors[] = 'Такая почта уже используется';
			}
			if(strpos((trim($_POST['login'])), 0x20) !== false) {
				$errors_n[] = 1;
				$errors[] = 'Логин не должен содержать пробелов';
			}
			if(strpos((trim($_POST['password'])), 0x20) !== false) {
				$errors_n[] = 3;
				$errors[] = 'Пароль не должен содержать пробелов';
			}
			if(strlen($_POST['password']) < 8) {
				$errors_n[] = 3;
				$errors[] = 'Пароль должен быть не меньше 8 символов';
			}
			if(preg_match("/[A-Z]/", $_POST['password']) == 0) {
				$errors_n[] = 3;
				$errors[] = 'Пароль должен иметь хотя бы 1 букву верхнего регистра';
			}
			if(preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $_POST['password']) == 0) {
				$errors_n[] = 3;
				$errors[] = 'Пароль должен иметь хотя бы 1 спецсимвол';
			}
			if(preg_match("/[a-z]/", $_POST['password']) == 0) {
				$errors_n[] = 3;
				$errors[] = 'Пароль должен иметь хотя бы 1 букву нижнего регистра';
			}
			if(preg_match("/[0-9]/", $_POST['password']) == 0) {
				$errors_n[] = 3;
				$errors[] = 'Пароль должен иметь хотя бы 1 цифру';
			}
			if($_POST['password'] != $_POST['password2']) {
				$errors_n[] = 4;
				$errors[] = 'Пароли не совпадают';
			}	
			if(empty($errors)){
				//Нет ошибок
				$client = R::dispense('clients');
				$client->email = trim(strtolower($_POST['email']));
				$client->login = trim(strtolower($_POST['login']));
				$client->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$client->country = $_POST['country'];
				$client->reg_d = date("Y-m-d");
				$client->last_t = date("Y-m-d H:i:s");
				$client->rec_t = date("Y-m-d H:i:s");
				$client->hash = md5($client->login.$client->last_t.$client->country);
				R::store($client);
				$subject = "=?utf-8?B?".base64_encode("Подтверждение почты")."?=";
				$headers = "From: $email\r\nContent-type: text/html;charset=utf-8\r\n";
				$link = "http://" . $_SERVER['HTTP_HOST'] ."/scripts/verified.php?l=". base64_encode($client->login) . "&h=" . $client->hash;
				$message = " <a href=$link class=\"btn btn-success\">Подтвердить почту</a><br/>Или по ссылке: $link";
				mail(trim($_POST['email']), $subject, $message, $headers);
				R::selectDatabase('postgres');
				R::exec('CREATE USER "' . $client->login .'" WITH PASSWORD \'' . $_POST['password'] .'\';');
				R::exec('GRANT "user" TO "' . $client->login .'";');
				R::selectDatabase('non_auth');
				header("Location: /auth.php");
				exit();
			}
			else {
				//Есть ошибки
				$_SESSION['errors_sm'] = $errors;
				$_SESSION['errors_sn'] = $errors_n;
				$_SESSION['data_s'] = [$_POST['email'], $_POST['login'], $_POST['country']];
				header("Location: /signup.php");
				
			}
		}
		else {
			//Пользователь случаной сюда попал
			header("Location: /");
		}