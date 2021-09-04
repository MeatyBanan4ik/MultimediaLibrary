<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$status = R::getAll('SELECT * FROM orders WHERE id_client = ? AND state = \'backet\'', [$_SESSION['id']]);
		if(empty($status)){
			$backet = R::dispense('orders');
			$backet->date = date("Y-m-d H:i:s");
			$backet->id_client = $_SESSION['id'];
			$backet->state = 'backet';
			$status[0][id] = R::store($backet);
		}
		$check = R::findOne('multimedia_list', 'id_order = ? AND id_multimedia = ?', [$status[0][id], array_keys($_POST)[0]]);
		
		if(!$check) {
			R::exec('INSERT INTO multimedia_list(id_order, id_multimedia) VALUES(?, ?)', [$status[0][id], array_keys($_POST)[0]]);
		}	
		header('Location: /backet.php');
	}
	else {
		header('Location: /');
	}