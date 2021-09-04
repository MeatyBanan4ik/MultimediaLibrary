<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$user = R::findOne('clients', "id = ?", [$_SESSION['id']]);
		$errors = [];
		if(!password_verify($_POST['old_p'], $user->password)) {
				$errors[] = 'Введите правильный старый пароль';
		}
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
		if(!empty($errors)) {
			$_SESSION['errors_settings'] = $errors;
			header('Location: /settings.php');
			exit();
		}
		else {
			$user->password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
			$user->rec_t = date("Y-m-d H:i:s");
			if($user->email_verified == false){
				$user->email_verified = 0;
			}
			R::store($user);
			R::selectDatabase('postgres');
			R::exec('ALTER ROLE "'. $user->login .'" WITH PASSWORD \''. $_POST['new_password'] .'\';');
			unset($_SESSION['id']);
			unset($_SESSION['login']);
			unset($_SESSION['password']);
			R::selectDatabase('non_auth');
			$hash = md5($user->hash.$user->rec_t);
			$subject = "=?utf-8?B?".base64_encode("Восстановление пароля")."?=";
			$headers = "From: CourseWork <meatybanana11@gmail.com>\r\nContent-type: text/html;charset=utf-8\r\n";
			$link = "http://" . $_SERVER['HTTP_HOST'] ."/remember.php?l=". base64_encode($user->login) . "&h=" . $hash;
			$message = "Пароль успешно изменен<br/>Если это были не вы нажмите на кнопку <a href=$link class=\"btn btn-success\">Восстановить пароль</a> или перейдите по ссылке: $link";
			mail(trim($user->email), $subject, $message, $headers);
		}

		header('Location: /');
	}
	else {
		header('Location: /');
	}