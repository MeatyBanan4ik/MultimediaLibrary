<?php
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])){
		require_once "../add/db.php";
		$check = R::findOne('orders', 'id_client = ? AND state = \'backet\'', [$_SESSION['id']]);
		if($check) {
			R::exec('DELETE FROM multimedia_list WHERE id_order = ? AND id_multimedia = ?', [$check->id, array_keys($_POST)[0]]);
		}
		header('Location: /backet.php');
	}
	else {
		header('Location: /');
	}