<?php session_start(); 
	require_once "./add/db.php";

	if(!empty($_GET['id'])):
		$multimedia = R::findOne('multimedia', 'id = ?', [$_GET[id]]);
		if(!$multimedia):
			header('Location: /');
			exit();
		else:
			$user = R::findOne('clients', "id = ?", [$_SESSION['id']]);
			if((($multimedia->id_client != $user->id and $user->rights == 'user') or !isset($_SESSION['id'])) and empty($multimedia->id_administrator) ){
				header('Location: /');
				exit();
			}
			if(isset($_SESSION['id'])){
						R::exec('UPDATE multimedia SET views = views + 1 WHERE id = ?', [$_GET['id']]);
					}?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<title><?php echo $multimedia->name?></title>
	<script src="css/jquery-3.2.1.slim.min.js"></script>
	<script src="css/popper.min.js"></script>
	<script src="css/bootstrap.min.js"></script>
</head>
<body>
	<?php require "blocks/header.php";?>
	<div class="container">
		<div class="row d-flex flex-row">
			<div style="max-width: 214px; position: relative;" class="col mt-3 d-flex flex-column text-center">
				<img style="width: 214px; height: 298px; object-fit: cover;" src="media\cover\<?php echo $multimedia->cover;?>">
				<?php if(isset($_SESSION['id'])):
						$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\'', [$_SESSION['id']]);
						$state = false;
						$auth = false;
						foreach ($mult_id as $m) {
							if($m[id_multimedia] == $_GET['id']) {
								$state = true;
							}
						}
						$aut = R::findOne('multimedia', 'id = ?', [$_GET['id']]);
						if($aut->id_client == $_SESSION['id']){
							$auth = true;
						}
						if($state or $auth):
							if($auth):?>
								<a style="width: 214px" href="/settings.php?type=multimedia&id=<?php echo $multimedia->id;?>" class="btn btn-outline-success mt-2">
									Редактировать
								</a>
							<?endif;?>
						<?php else:
					?>
				<form method="POST" action="scripts/in_backet.php">
					<button style="width: 214px" name="<?php echo $multimedia->id;?>" class="btn btn-outline-light mt-2">
						Купить
					</button>
				</form>
				<?php endif;
					else:?>
					<a style="width: 214px" class="btn btn-outline-light mt-2" href="auth.php">Войти</a>
				<?php endif;?>
				<?php if(isset($_SESSION['id']) and $user->rights == 'creator' and !empty($multimedia->id_administrator)):?>
					<form action="scripts/recheck.php" method="POST">
						<button style="width: 214px" name="<?php echo $multimedia->id;?>" class="btn btn-outline-danger mt-2">
							Отправить на проверку
						</button>
					</form>
				<?php endif;?>
				<?php if(isset($_SESSION['id']) and $user->rights != 'user' and empty($multimedia->id_administrator) and $multimedia->id_client != $_SESSION['id']):?>
					<a style="width: 214px" class="btn btn-outline-primary mt-2" download href="media/<?echo $multimedia->type.'/'.$multimedia->id.'.zip';?>">Скачать</a>
					<form action="scripts/check.php" method="POST">
						<input type="text" hidden name='id' value="<?echo $_GET['id'];?>">
						<button style="width: 214px" name='verify' class="btn btn-outline-success mt-2">Пропустить</button>
						<button style="width: 214px" name='delete' class="btn btn-outline-danger mt-2">Удалить</button>
					</form>
				<?php endif;?>
				<?php if (isset($_SESSION['id']))$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\' AND multimedia_list.id_multimedia = ?', [$_SESSION['id'], $_GET['id']]);
					$state = false;
					if($mult_id){
						$state = true;
					}
					
					if($state == true):?>
						<a style="width: 214px" class="btn btn-outline-primary mt-2" download href="media/<?echo $multimedia->type.'/'.$multimedia->id.'.zip';?>">Скачать</a>
					<?php endif;?>
			</div>
			<div class="col">
				<div class="row d-flex flex-column">
					<div class="col d-flex flex-row">					
						<div class="col mt-3">
							<div class="row d-flex flex-column">
								<div class="col">
									<h4 style="word-wrap: break-word; word-break: break-all;">
										<?php echo $multimedia->name;?>
									</h4>
									<div class="row">
										<div class="col">
											<h6>
												Просмотры: <?php echo $multimedia->views;?>
											</h6>
										</div>
										<div class="col">
											<h6 class="text-right">
												Цена: <?php echo $multimedia->price . " UAH";?>
											</h6>
										</div>
									</div>
									
								</div>
								<div class="col">
									<h5>Описание: </h5>
									<p style="word-wrap: break-word; word-break: break-all;">
										<?php echo $multimedia->description;?>
									</p>
								</div>
							</div>
						</div>
						<div class="col-3 mt-3">
							<h6>Автор:</h6>
							<?php $author = R::findOne('clients', 'id = ?', [$multimedia->id_client]);
								if($author){
									echo "<a class='text-decoration-none' href=\"/profile.php?id=$author->id\">$author->login</a>";
								}
								else {
									echo 'Удален';
								}?>
							<h6>Страна:</h6>
							<a href="/view.php?country=<?php echo $multimedia->country?>" class="text-decoration-none"><?php echo $multimedia->country?></a>
							<?php $tags = R::getAll('SELECT id_tag FROM tags_list WHERE id_multimedia = ?', [$multimedia->id]);
								if(!empty($tags)):
								?>
									<h6>Теги:</h6>
									<?php 
										foreach ($tags as $t):?>
											<a class="text-decoration-none" href="/view.php?tag=<?php  $name = R::findOne('tags', 'id = ?', [$t[id_tag]]); echo $name->name;?>"><?php echo $name->name;?></a> 
										<?php endforeach;?>
								<?php endif;?>
							<div class="mt-2">
								<h6>Средняя оценка: </h6>
								<p><?php echo $multimedia->avg_mark?></p>
							</div>
						</div>
					</div>
					
				</div>

			</div>
		</div>
		<div class="row d-flex flex-row">
			<div style="max-width: 250px;" class="col p-3">

			</div>
			<div class="col">
				<div class="row d-flex flex-column">
					<div class="col">
						<?php if(isset($_SESSION['id'])):?>
							<form class="text-right" action="scripts/comments.php" method="POST">
								<textarea name="text"  style="width: 100%;  height: 100px;" class="form-control mb-1 p-3" required> </textarea> 
								<button name="<?php echo $multimedia->id;?>" class="btn btn-outline-light">Отправить комментарий</button>
							</form>
						<?php endif;?>
					</div>
					<div class="col">
						<?php 
							$comments = R::getAll('SELECT clients.login, clients.id, clients.rights, comments.id as "comments_id", clients.avatar, comments.date, comments.text FROM comments JOIN clients ON clients.id = comments.id_client WHERE id_multimedia = ? ORDER BY date DESC', [$multimedia->id]);
								foreach ($comments as $c):?>
							<div style="" class="row d-flex flex-row  mt-2">
								<div style="max-width: 130px;"class="col d-flex  p-3">
									<a href="/profile.php?id=<?php echo $c[id];?>"><img style="width: 130px; height: 170px; object-fit: cover;"src="media/avatar/<?php echo $c[avatar];?>"> 
									</a>
								
									<?php if($c[id] == $multimedia->id_client):?>
										<p style="position: absolute; top: 25px; right: -16px; background-color: red; border-radius: 50px 10px 10px 50px;"> Автор </p>
									<?php endif;?>
							</div>
								<div class="col d-flex flex-column p-3 ml-3 ">
									<p style="position: absolute; top: 5px; right: 10px;"><?php echo date('H:i d.m.Y', strtotime($c[date]));?></p>
									<a class="text-decoration-none <?php echo $c[rights] != 'user' ? 'text-danger' : 'text-white';?>" href="/profile.php?id=<?php echo $c[id];?>"><?php echo $c[login];?></a>
									<p style="word-wrap: break-word;" class="mt-2"><?php echo $c[text]?></p>
									<?php 
										if(isset($_SESSION['id'])):
											if($user->rights != "user" or $c[id] == $_SESSION['id']):?>
												<form action="scripts/delete_comment.php" method="POST">
													<button style="position: absolute; bottom: 15px; right: 15px;" name="<?php echo $c["comments_id"];?>" class="btn btn-outline-danger mt-2 text-right">Удалить</button>
													<input hidden name=<?php echo $_GET['id']?>>
												</form>
										<?php endif;
											endif;?>
								</div>
							</div>
						<?php
							endforeach;
						?>
					</div>
				</div>
				
			</div>
			<div style="max-width: 250px;" class="col p-3"></div>
		</div>
	</div>
	<?php require "blocks/footer.php" ?>
</body>
</html>
<?php 

endif;
	else:
		header('Location: /');
		endif;?>