<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		if(isset($_POST['change'])){
			$user = R::findOne('clients', 'id = ?', [$_POST['id']]);
			R::exec('UPDATE clients SET rights = ? WHERE id = ?', [$_POST['role'], $_POST['id']]);
			R::selectDatabase('postgres');
			R::exec('REVOKE "'. $user->rights .'" FROM "' . $user->login .'";');
			R::exec('GRANT "' . $_POST['role'] .'" TO "' . $user->login .'";' );
			R::selectDatabase($_SESSION['id']);
			header('Location: /admin.php');
			exit();
		}
		elseif (isset($_POST['delete'])) {
			$user = R::findOne('clients', 'id = ?', [$_POST['id']]);
			R::exec('DELETE FROM clients WHERE id = ?', [$_POST['id']]);
			R::selectDatabase('postgres');
			R::exec('DROP OWNED BY "'.$user->login.'";');
			R::exec('DROP ROLE "'.$user->login.'";');
			R::selectDatabase($_SESSION['id']);
			header('Location: /admin.php');
			exit();
		}
		header('Location: /admin.php');
		exit();
	}
	else {
		header('Location: /');
		exit();
	}