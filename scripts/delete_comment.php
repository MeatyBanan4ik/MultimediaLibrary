<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		R::exec('DELETE FROM comments WHERE id = ?', [array_keys($_POST)[0]]);	
		header('Location: /template.php?id='.array_keys($_POST)[1]);
		exit();
	}
	else {
		header('Location: /');
	}