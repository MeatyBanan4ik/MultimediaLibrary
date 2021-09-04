<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Библиотека</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<script src="css/jquery-3.2.1.slim.min.js"></script>
	<script src="css/popper.min.js"></script>
	<script src="css/bootstrap.min.js"></script>
</head>
<body>
	<?php require "blocks/header.php";?>
	<div class="container">
		<div class="row d-flex flex-column">
			<div class="col p-3 ">
				<form class="d-flex flex-row" action="view.php" method="GET">
					<select class="custom-select" style="width: 100px" name='type'>
						<option value="" selected>Тип</option>
						<?php $type = R::getAll('SELECT DISTINCT type FROM multimedia WHERE id_administrator IS NOT NULL');
						foreach($type as $t):?>
							<option value="<?echo $t[type]?>"><?echo $t[type]?></option>
						<?php endforeach;?>
					</select>
					<select class="custom-select ml-2" style="width: 100px" name='country'>
						<option value="" selected>Страна</option>
						<?php $country = R::getAll('SELECT DISTINCT country FROM multimedia WHERE id_administrator IS NOT NULL');
						foreach($country as $c):?>
							<option value="<?echo $c[country]?>"><?echo $c[country]?></option>
						<?php endforeach;?>
					</select>
					<input style="width: 170px;" class="mr-2 ml-2 form-control" type="text" name="text">
					<button class="btn btn-outline-light" autofocus>Поиск</button>
				</form>
			</div>
			<div class="col d-flex flex-row p-3">
				<?php 
					$query = (empty($_GET['country']) ? '' : ' AND multimedia.country = ? ') . (empty($_GET['type']) ? '' : ' AND multimedia.type = ? ') . (empty($_GET['text']) ? '' : ' AND (multimedia.name ilike ? OR multimedia.description ilike ?) ') . (empty($_GET['tag']) ? '' : ' AND tags.name = ? ');
					$object = [];
					
					if(!empty($_GET['country'])) {
						$object[] = $_GET['country'];
					}
					if(!empty($_GET['type'])) {
						$object[] = $_GET['type'];
					}
					if(!empty($_GET['text'])) {
						$object[] = '%'.$_GET['text'].'%';
						$object[] = '%'.$_GET['text'].'%';
					}
					if(!empty($_GET['tag'])) {
						$object[] = $_GET['tag'];
					}
					$multimedia = R::getAll('SELECT DISTINCT multimedia.cover, multimedia.create_date, multimedia.id_client, multimedia.name, multimedia.id, multimedia.price FROM multimedia LEFT JOIN tags_list ON multimedia.id = tags_list.id_multimedia LEFT JOIN tags ON tags.id = tags_list.id_tag WHERE multimedia.id_administrator IS NOT NULL '. $query . ' ORDER BY multimedia.create_date DESC', $object); ?>
				<?php if(!empty($multimedia)):?>
							<div class="row">
							<?php foreach($multimedia as $m):?>
								<div class="col mr-2 mt-2" style="max-width: 214px; max-height: 298px; position: relative;">
									<a class="text-decoration-none text-white" href="<?php echo 'template.php?id='.$m[id]?>"><img style="width: 214px; height: 298px; object-fit: cover;" src="media\cover\<?php echo $m[cover];?>">
									<p style="position: absolute; top: 0; right: -15px; background-color: black; border-radius: 100px 10px 10px 100px;"><?php echo " ".$m[price]." UAH"?></p>
									<p style="position: absolute; bottom: -16px; backdrop-filter: blur(50px); width: 100%; background: rgba(0, 0, 0, 0.5);"class="text-truncate text-center"><?php echo $m[name]?></p>
									<?php if(isset($_SESSION['id'])):
									$mult_id = R::getAll('SELECT multimedia_list.id_multimedia FROM orders JOIN multimedia_list ON multimedia_list.id_order = orders.id WHERE id_client = ? AND state = \'paid\' AND multimedia_list.id_multimedia = ?', [$_SESSION['id'], $m[id]]);
									$state = false;
									$auth = false;
									if($mult_id){
										$state=true;
									}
									if($m[id_client] == $_SESSION['id']){
										$auth = true;
									}
									if($state or $auth):?>
										<p style="position: absolute; top: 30px; right: -15px; background-color: <?php echo $auth ? 'red' : 'green';?>; border-radius: 100px 10px 10px 100px;"><?php echo $auth ? ' Автор ' : ' Куплено '?></p></a>
							<?php endif;
									endif;?>
								</div>
							<?php endforeach;?>
							</div>
						<?php else:?>
							<h6>По такому запросу ничего нет</h6>
   					<?php endif;?>
			</div>
		</div>
	</div>
	<?php require "blocks/footer.php" ?>
</body>
</html>