<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$subject = "=?utf-8?B?".base64_encode(isset($_POST['verify']) ? "Ваше мультимедиа было одобрено к продаже" : "Ваше мультимедиа было удалено")."?=";
		$headers = "From: $email\r\nContent-type: text/html;charset=utf-8\r\n";
		$message = isset($_POST['verify']) ? ("Ваше <a href='".$_SERVER['HTTP_HOST']."/template.php?id=".$_POST['id']."'>мультимедиа ".R::findOne('multimedia', 'id = ?', [$_POST['id']])->name."</a> было одобрено к продаже") : ("Ваше мультимедиа ".R::findOne('multimedia', 'id = ?', [$_POST['id']])->name." было удалено, можете связаться с нашим модератором по почте ". R::findOne('clients', 'id = ?', [$_SESSION['id']])->email);
		if(isset($_POST['verify'])){
			R::exec('UPDATE multimedia SET id_administrator = ? WHERE id = ?', [$_SESSION['id'], $_POST['id']]);
			mail(R::findOne('clients', 'id = ?', [R::findOne('multimedia', 'id = ?', [$_POST['id']])->id_client])->email, $subject, $message, $headers);
			header('Location: /template.php?id='.$_POST['id']);			
			exit();
		}
		elseif (isset($_POST['delete'])) {
			mail(R::findOne('clients', 'id = ?', [R::findOne('multimedia', 'id = ?', [$_POST['id']])->id_client])->email, $subject, $message, $headers);
			unlink('../media/'.R::findOne('multimedia', 'id = ?', [$_POST['id']])->type.'/'.$_POST['id'].'.zip');
			R::exec('DELETE FROM multimedia WHERE id = ?', [$_POST['id']]);			
			header('Location: /admin.php?type=multimedia');
			exit();
		}
		header('Location: /admin.php?type=multimedia');
		exit();
	}
	else {
		header('Location: /');
	}