<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$errors = [];
		if(strlen($_POST['name']) < 3){
			$errors[] = 'Имя должно иметь больше 3 символов';
		}
		if(strlen($_POST['name']) > 60){
			$errors[] = 'Имя должно иметь меньше 60 символов';
		}
		if(strlen($_POST['text']) > 300){
			$errors[] = 'Описание должно быть меньше 300 символов';
		}
		if(strlen($_POST['text']) < 10){
			$errors[] = 'Описание должно быть больше 10 символов';
		}
		if($_POST['price'] < 0){
			$errors[] = 'Цена не может быть меньше 0';
		}
		if($_POST['price'] > 10000){
			$errors[] = 'Цена не может быть больше 10 000 грн';
		}
		if(empty($errors)){
			R::exec('UPDATE multimedia SET description = ?, name = ?, price = ? WHERE id = ?', [$_POST['text'], $_POST['name'], $_POST['price'], array_keys($_POST)[3]]);
		}
		else {
			$_SESSION['errors_settings'] = $errors;
		}
		header('Location: /settings.php?type=multimedia&id='.array_keys($_POST)[3]);
		exit();
	}
	else {
		header('Location: /');
	}