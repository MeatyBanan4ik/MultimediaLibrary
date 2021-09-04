<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		R::exec('UPDATE multimedia SET id_administrator = NULL WHERE id = ?', [array_keys($_POST)[0]]);	
		header('Location: /template.php?id='.array_keys($_POST)[0]);
		exit();
	}
	else {
		header('Location: /');
		exit();
	}
header('Location: /');