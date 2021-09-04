<?php 
session_start();
require_once "../add/db.php";
if(isset($_POST['login']) && isset($_POST['password'])){
	//Есть пост запрос
	$errors = array();
	if(filter_var($_POST['login'], FILTER_VALIDATE_EMAIL)) {
		//Вход через почту
		$user = R::findOne('clients', "email = ?", [trim(strtolower($_POST['login']))]);

	}
	else {
		//Вход через логин
		$user = R::findOne('clients', "login = ?", [trim(strtolower($_POST['login']))]);
	}
	if($user) {
		//Логин/почта зарегистрированы
		if(password_verify($_POST['password'], $user->password))
		{
			//Успешно вошел
			$_SESSION['id'] = $user->id;
			$_SESSION['login'] = $user->login;
			$_SESSION['password'] = $_POST['password'];


			header("Location: /");
			exit();

		}
		else {
			//Не правильный пароль
			$errors[] = 'Не правильная комбинация ' . (filter_var($_POST['login'], FILTER_VALIDATE_EMAIL) ? "почта" : "логин") . '/пароль';
		}
	}
	else {
		//Не правильный логин/почта
		$errors[] = 'Не существует такого пользователя';
		$errors[] = "1";
	}
	if(!empty($errors)) {
		$_SESSION['errors_am'] = $errors;
		$_SESSION['data_a'] = $_POST['login'];
		header("Location: /auth.php");
		exit();
}
}
else {
	header("Location: /");
	exit();
}
header("Location: /");
exit();