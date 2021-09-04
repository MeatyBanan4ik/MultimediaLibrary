<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$fileNameCmps = explode(".", $_FILES['cover']['name']);
		$fileExtension = strtolower(end($fileNameCmps));
		if(in_array($fileExtension, ['jpg', 'png', 'jpeg'])) {
			$fileTmpPath = $_FILES['cover']['tmp_name'];
			$fileName = $_FILES['cover']['name'];
			$newFileName = md5(time() . $fileName) . '.' . $fileExtension;
			if(move_uploaded_file($fileTmpPath, ('../media/cover/'.$newFileName))){
				R::exec('UPDATE multimedia SET cover = ? WHERE id = ?', [$newFileName, array_keys($_POST)[0]]);
			}
		}
		else {
			header('Location: /settings.php?type=multimedia&id='.array_keys($_POST)[0]);
		}
		header('Location: /settings.php?type=multimedia&id='.array_keys($_POST)[0]);
	}
	else {
		header('Location: /');
	}