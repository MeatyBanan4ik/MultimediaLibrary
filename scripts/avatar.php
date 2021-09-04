<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$fileNameCmps = explode(".", $_FILES['avatar']['name']);
		$fileExtension = strtolower(end($fileNameCmps));
		if(in_array($fileExtension, ['jpg', 'png', 'jpeg'])) {
			$fileTmpPath = $_FILES['avatar']['tmp_name'];
			$fileName = $_FILES['avatar']['name'];
			$newFileName = md5(time() . $fileName) . '.' . $fileExtension;
			if(move_uploaded_file($fileTmpPath, ('../media/avatar/'.$newFileName))){
				R::exec('UPDATE clients SET avatar = ? WHERE id = ?', [$newFileName, $_SESSION['id']]);
			}
		}
		else {
			header('Location: /settings.php');
		}
		header('Location: /settings.php');
	}
	else {
		header('Location: /');
	}