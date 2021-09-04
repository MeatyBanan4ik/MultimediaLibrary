<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$mark = R::findOne('mark', 'id_client = ? AND id_multimedia = ?', [$_SESSION['id'], array_keys($_POST)[1]]);
		if($mark) {
			R::exec('UPDATE mark SET value = ? WHERE id_multimedia = ? AND id_client = ?', [$_POST['value'], array_keys($_POST)[1], $_SESSION['id']]);
		}
		else {
			R::exec('INSERT INTO mark(value, id_multimedia, id_client) VALUES (?, ?, ?)', [$_POST['value'], array_keys($_POST)[1], $_SESSION['id']]);
		}
		header('Location: /settings.php?type=order_list&id='. array_keys($_POST)[2]);

	}

	else {
		header('Location: /');
	}