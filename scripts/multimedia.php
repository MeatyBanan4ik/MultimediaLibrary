<?php 
	session_start();
	if(isset($_POST) AND isset($_SESSION['id'])) {
		require_once "../add/db.php";
		$errors = [];
		if(strlen($_POST['name']) < 3){
			$errors[] = 'Имя должно иметь больше 3 символов';
		}
		if(strlen($_POST['name']) > 60){
			$errors[] = 'Имя должно иметь меньше 60 символов';
		}
		if(strlen($_POST['text']) > 300){
			$errors[] = 'Описание должно быть меньше 300 символов';
		}
		if(strlen($_POST['text']) < 10){
			$errors[] = 'Описание должно быть больше 10 символов';
		}
		if($_POST['price'] < 0){
			$errors[] = 'Цена не может быть меньше 0';
		}
		if($_POST['price'] > 10000){
			$errors[] = 'Цена не может быть больше 10 000 грн';
		}
		if($_POST['tags']){
				if(count(explode(" ", trim($_POST['tags']))) > 5){
					$errors[] = 'Количество тегов не может быть больше 5';
				}
				foreach (explode(" ", trim($_POST['tags'])) as $t) {
						if(strlen($t) < 3){
							$errors[] = 'Тег не может иметь меньше 3 символов';
						}
						if(strlen($t) > 30){
							$errors[] = 'Тег не может иметь больше 30 символов';
						}
					}
				}
		if(empty($errors)){
				$multimedia = R::dispense('multimedia');
				if(!empty($_FILES['cover']['name'])){
					$fileNameCmps = explode(".", $_FILES['cover']['name']);
					$fileExtension = strtolower(end($fileNameCmps));
					if(in_array($fileExtension, ['jpg', 'png', 'jpeg'])) {
						$fileTmpPath = $_FILES['cover']['tmp_name'];
						$fileName = $_FILES['cover']['name'];
						$newFileName = md5(time() . $fileName) . '.' . $fileExtension;
						move_uploaded_file($fileTmpPath, ('../media/cover/'.$newFileName));
						$multimedia->cover = $newFileName;
					}
					else {
						$errors[] = 'Отправьте корректное фото';
						$_SESSION['errors_settings'] = $errors;
						header('Location: /settings.php?type=sell');
						exit();
					}
				}				
				$multimedia->name = trim($_POST['name']);
				$multimedia->description = $_POST['text'];
				$multimedia->create_date = date("Y-m-d");
				$multimedia->country = $_POST['country'];
				$multimedia->type = $_POST['type'];
				$multimedia->price = round($_POST['price'], 2);
				$multimedia->id_client = $_SESSION['id'];
				$multimedia = R::store($multimedia);
				$zip = new ZipArchive();
				$file = "../media/".$_POST['type']."/$multimedia.zip";
				$zip->open($file, ZipArchive::CREATE);
				for($i = 0; $i < count($_FILES['multimedia']['name']); $i++) {
					$zip->addFile($_FILES['multimedia']['tmp_name'][$i], $_FILES['multimedia']['name'][$i]);			
				}
				$zip->close();
				if($_POST['tags']){
					foreach (explode(" ", trim($_POST['tags'])) as $t) {
						$tag = R::findOne('tags', 'name = ?', [$t]);
						if(!$tag) {
							$tag = R::dispense('tags');
							$tag->name = $t;
							$tag->id = R::store($tag);
						}
						if(!R::findOne('tags_list', 'id_multimedia = ? AND id_tag = ?', [$multimedia, $tag->id])){
							R::exec('INSERT INTO tags_list (id_multimedia, id_tag) VALUES(?, ?)', [$multimedia, $tag->id]);
						}
					}
				}
				header('Location: /template.php?id='.$multimedia);
				exit();
			}
		else {
			$_SESSION['errors_settings'] = $errors;
			header('Location: /settings.php?type=sell');
			exit();
		}
		
	}
	else {
		header('Location: /');
		exit();
	}
	header('Location: /settings.php?type=sell');