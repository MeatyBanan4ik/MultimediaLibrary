<?php session_start(); require_once "./add/db.php";?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Главная</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<script src="css/jquery-3.2.1.slim.min.js"></script>
	<script src="css/popper.min.js"></script>
	<script src="css/bootstrap.min.js"></script>
</head>
<body>
	<?php require "blocks/header.php";?><p>
	<div class="container">
		<div class="row d-flex flex-column">
			<div class="col p-3">
				<hr style="color: white; background-color: white;">
				<div class="d-flex flex-row justify-content-between">
					<?php  $popular = R::getAll('SELECT * FROM multimedia WHERE id_administrator IS NOT NULL ORDER BY views DESC LIMIT 5');?>
					<p>Популярное</p>
					<a href="/view.php" class="btn btn-outline-light">Еще</a>
				</div>
				<div class="row text-center p-1 mt-1">
					<?php foreach($popular as $p):?>
						<div class="col mr-2" style="max-width: 214px; max-height: 298px; position: relative;">
							<a href="<?php echo 'template.php?id='.$p[id]?>" class='text-non-decoration text-white'><img style="width: 214px; height: 298px; object-fit: cover;" src="media\cover\<?php echo $p[cover];?>">
							<p style="position: absolute; top: 0; right: -15px; background-color: black; border-radius: 100px 10px 10px 100px;"><?php echo " ".$p[price]." UAH"?></p>
							<p style="position: absolute; bottom: -16px; backdrop-filter: blur(50px); width: 100%; background: rgba(0, 0, 0, 0.5);"class="text-truncate"><?php echo $p[name]?></p>
							<?php if(isset($_SESSION['id'])):
									$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\' AND multimedia_list.id_multimedia = ?', [$_SESSION['id'], $p[id]]);
									$state = false;
									$auth = false;
									if($mult_id){
										$state=true;
									}
									$aut = R::findOne('multimedia', 'id = ?', [$p[id]]);
									if($aut->id_client == $_SESSION['id']){
										$auth = true;
									}
									if($state or $auth):?>
										<p style="position: absolute; top: 30px; right: -15px; background-color: <?php echo $auth ? 'red' : 'green';?>; border-radius: 100px 10px 10px 100px;"><?php echo $auth ? ' Автор ' : ' Куплено '?></p>
							<?php endif;
									endif;?>
						</a></div>
					<?php endforeach;?>
				</div>
			</div>
      		<div  class="col p-3">
      			<hr style="color: white; background-color: white;">
      			<div class="d-flex flex-row justify-content-between">
      				<?php  $new = R::getAll('SELECT * FROM multimedia WHERE id_administrator IS NOT NULL ORDER BY create_date DESC LIMIT 5');?>
					<p>Новое</p>
					<a href="/view.php" class="btn btn-outline-light">Еще</a>
				</div>
				<div class="row text-center p-1 mt-1">
					<?php foreach($new as $p):?>
						<div class="col mr-2" style="max-width: 214px; max-height: 298px; position: relative;">
							<a href="<?php echo 'template.php?id='.$p[id]?>"><img style="width: 214px; height: 298px; object-fit: cover;" src="media\cover\<?php echo $p[cover];?>"></a>
							<p style="position: absolute; top: 0; right: -15px; background-color: black; border-radius: 100px 10px 10px 100px;"><?php echo " ".$p[price]." UAH"?></p>
							<p style="position: absolute; bottom: -16px; backdrop-filter: blur(50px); width: 100%; background: rgba(0, 0, 0, 0.5);"class="text-truncate"><?php echo $p[name]?></p>
							<?php if(isset($_SESSION['id'])):
									$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\' AND multimedia_list.id_multimedia = ?', [$_SESSION['id'], $p[id]]);
									$state = false;
									$auth = false;
									if($mult_id){
										$state=true;
									}
									$aut = R::findOne('multimedia', 'id = ?', [$p[id]]);
									if($p[id_client] == $_SESSION['id']){
										$auth = true;
									}
									if($state or $auth):?>
										<p style="position: absolute; top: 30px; right: -15px; background-color: <?php echo $auth ? 'red' : 'green';?>; border-radius: 100px 10px 10px 100px;"><?php echo $auth ? ' Автор ' : ' Куплено '?></p>
							<?php endif;
									endif;?>

						</div>
					<?php endforeach;?>
				</div>
      		</div>
      		<div class="col p-3">
      			<hr style="color: white; background-color: white;">
      			<div class="d-flex flex-row justify-content-between">
      				<?php  $photo = R::getAll("SELECT * FROM multimedia WHERE type='photo' AND id_administrator IS NOT NULL ORDER BY create_date DESC LIMIT 5");?>
					<p>Фото</p>
					<a href="/view.php?type=photo" class="btn btn-outline-light">Еще</a>
				</div>
				<div class="row text-center p-1 mt-1">
					<?php foreach($photo as $p):?>
						<div class="col mr-2" style="max-width: 214px; max-height: 298px; position: relative;">
							<a href="<?php echo 'template.php?id='.$p[id]?>"><img style="width: 214px; height: 298px; object-fit: cover;" src="media\cover\<?php echo $p[cover];?>"></a>
							<p style="position: absolute; top: 0; right: -15px; background-color: black; border-radius: 100px 10px 10px 100px;"><?php echo " ".$p[price]." UAH"?></p>
							<p style="position: absolute; bottom: -16px; backdrop-filter: blur(50px); width: 100%; background: rgba(0, 0, 0, 0.5);"class="text-truncate"><?php echo $p[name]?></p>
							<?php if(isset($_SESSION['id'])):
									$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\' AND multimedia_list.id_multimedia = ?', [$_SESSION['id'], $p[id]]);
									$state = false;
									$auth = false;
									if($mult_id){
										$state=true;
									}
									$aut = R::findOne('multimedia', 'id = ?', [$p[id]]);
									if($p[id_client] == $_SESSION['id']){
										$auth = true;
									}
									if($state or $auth):?>
										<p style="position: absolute; top: 30px; right: -15px; background-color: <?php echo $auth ? 'red' : 'green';?>; border-radius: 100px 10px 10px 100px;"><?php echo $auth ? ' Автор ' : ' Куплено '?></p>
							<?php endif;
									endif;?>
						</div>
					<?php endforeach;?>
				</div>
      		</div>
     		<div class="col p-3">
     			<hr style="color: white; background-color: white;">
     			<div class="d-flex flex-row justify-content-between">
     				<?php  $video = R::getAll("SELECT * FROM multimedia WHERE type='video' AND id_administrator IS NOT NULL ORDER BY create_date DESC LIMIT 5");?>
					<p>Видео</p>
					<a href="/view.php?type=video" class="btn btn-outline-light">Еще</a>
				</div>
				<div class="row text-center p-1 mt-1">
					<?php foreach($video as $p):?>
						<div class="col mr-2" style="max-width: 214px; max-height: 298px; position: relative;">
							<a href="<?php echo 'template.php?id='.$p[id]?>"><img style="width: 214px; height: 298px; object-fit: cover;" src="media\cover\<?php echo $p[cover]; ?>"></a>
							<p style="position: absolute; top: 0; right: -15px; background-color: black; border-radius: 100px 10px 10px 100px;"><?php echo " ".$p[price]." UAH"?></p>
							<p style="position: absolute; bottom: -16px; backdrop-filter: blur(50px); width: 100%; background: rgba(0, 0, 0, 0.5);"class="text-truncate"><?php echo $p[name]?></p>
							<?php if(isset($_SESSION['id'])):
									$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\' AND multimedia_list.id_multimedia = ?', [$_SESSION['id'], $p[id]]);
									$state = false;
									$auth = false;
									if($mult_id){
										$state=true;
									}
									$aut = R::findOne('multimedia', 'id = ?', [$p[id]]);
									if($p[id_client]  == $_SESSION['id']){
										$auth = true;
									}
									if($state or $auth):?>
										<p style="position: absolute; top: 30px; right: -15px; background-color: <?php echo $auth ? 'red' : 'green';?>; border-radius: 100px 10px 10px 100px;"><?php echo $auth ? ' Автор ' : ' Куплено '?></p>
							<?php endif;
									endif;?>
						</div>
					<?php endforeach;?>
				</div>
     		</div>
      		<div class="col p-3">
      			<hr style="color: white; background-color: white;">
      			<div class="d-flex flex-row justify-content-between">
      				<?php  $audio = R::getAll("SELECT * FROM multimedia WHERE type='audio' AND id_administrator IS NOT NULL ORDER BY create_date DESC LIMIT 5");?>
					<p>Аудио</p>
					<a href="/view.php?type=audio" class="btn btn-outline-light">Еще</a>
				</div>
				<div class="row text-center p-1 mt-1">
					<?php foreach($audio as $p):?>
						<div class="col mr-2" style="max-width: 214px; max-height: 298px; position: relative;">
							<a href="<?php echo 'template.php?id='.$p[id]?>"><img style="width: 214px; height: 298px; object-fit: cover;" src="media\cover\<?php echo $p[cover];?>"></a>
							<p style="position: absolute; top: 0; right: -15px; background-color: black; border-radius: 100px 10px 10px 100px;"><?php echo " ".$p[price]." UAH"?></p>
							<p style="position: absolute; bottom: -16px; backdrop-filter: blur(50px); width: 100%; background: rgba(0, 0, 0, 0.5);"class="text-truncate"><?php echo $p[name]?></p>
							<?php if(isset($_SESSION['id'])):
									$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\' AND multimedia_list.id_multimedia = ?', [$_SESSION['id'], $p[id]]);
									$state = false;
									$auth = false;
									if($mult_id){
										$state=true;
									}
									if($p[id_client] == $_SESSION['id']){
										$auth = true;
									}
									if($state or $auth):?>
										<p style="position: absolute; top: 30px; right: -15px; background-color: <?php echo $auth ? 'red' : 'green';?>; border-radius: 100px 10px 10px 100px;"><?php echo $auth ? ' Автор ' : ' Куплено '?></p>
							<?php endif;
									endif;?>
						</div>
					<?php endforeach;?>
				</div>
      		</div>
		</div>
	</div>
	<?php require "blocks/footer.php" ?>
</body>
</html>