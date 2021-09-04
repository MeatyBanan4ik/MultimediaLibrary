<?php 
	session_start();
	require_once "../add/db.php";
	if(isset($_GET)) {
		$hash = R::findOne('clients', "login = ?", [mb_convert_encoding(base64_decode($_GET['l']), "UTF-8")]);
		if ($_GET['h'] == $hash->hash) {
			if($hash->email_verified == false) {
				//успешно зарегистрирован
				$hash->email_verified = true;
				R::store($hash);
				header("Location: /auth.php");
				exit();
			}
			else {
				//Уже подтвержден
				header("Location: /");
			}
		}
		else {
			//не правильный логин или хеш
			header("Location: /");
		}
	}
	else {
		//а где хеш?
		header("Location: /");
	}
	