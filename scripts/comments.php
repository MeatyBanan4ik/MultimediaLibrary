<?php
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])){
		require_once "../add/db.php";
		if(isset($_POST['text']) and !empty(trim($_POST['text']))){
			$comment = R::dispense('comments');
			$comment->text = trim($_POST['text']);
			$comment->date = date("Y-m-d H:i:s");
			$comment->id_client = $_SESSION['id'];
			$comment->id_multimedia = array_keys($_POST)[1];
			R::store($comment);
			header('Location: /template.php?id='.(array_keys($_POST)[1]));
		}
		else {
			if(isset($_POST['text'])){
				header('Location: /template.php?id='.(array_keys($_POST)[1]));
			} else {
				header('Location: /');
			}
		}
	}
	else {
		header('Location: /');
	}