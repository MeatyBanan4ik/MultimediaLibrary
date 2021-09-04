<?php session_start(); 
require_once "./add/db.php"; 
	if(!empty($_GET[id])):
		$client = R::findOne('clients', 'id = ?', [$_GET[id]]);
		if(!$client):
			header('Location: /');
		else:?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $client->login;?></title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<script src="css/jquery-3.2.1.slim.min.js"></script>
	<script src="css/popper.min.js"></script>
	<script src="css/bootstrap.min.js"></script>
</head>
<body>
	<?php require "blocks/header.php";?>
		<div class="container">
			<div class="row d-flex flex-row">
				<div style="max-width: 214px;" class="col p-3 text-center">
					<img style="width: 214px; height: 298px; object-fit: cover;"src="media/avatar/<?php echo $client->avatar;?>"> 
					<p class="ml-3"><?php echo date('H:i d.m.Y', strtotime($client->last_t))?></p>
				</div>
				<div class="col">
					<div class="row d-flex flex-column">
						<div class="col p-3 ml-3">
							<h5><?php echo $client->login?></h5>
						</div>
					</div>
				</div>
				<div class="col-3 mt-3">
					<h6>Страна:</h6>
					<p><?php echo $client->country?></p>
					<h6>Дата регистрации:</h6>
					<p><?php echo date('d.m.Y', strtotime($client->reg_d));?></p>
				</div>
			</div>
			<?php $multimedia = R::getAll('SELECT * FROM multimedia WHERE id_client = ? AND id_administrator IS NOT NULL ORDER BY create_date DESC', [$client->id]);
					if($multimedia):?>
			<div class="row d-flex flex-row">
				<div style="max-width: 214px;" class="col p-3">
				</div>
				<div class="col">
					<div class="row d-flex flex-column">
						<div style="width: 100%;" class="col">
							<?php foreach($multimedia as $m):?>
								<div style="" class="row d-flex flex-row  mt-2">
									<div style="max-width: 130px;"class="col d-flex flex-column p-3">
										<a href="/template.php?id=<?php echo $m[id];?>"><img style="width: 130px; height: 170px; object-fit: cover;"src="media/cover/<?php echo $m[cover];?>"> 
										</a>
										<p class="text-nowrap text-center ml-4 mb-0"><?php echo $m[price]?> UAH</p>
								</div>
									<div class="col d-flex flex-column p-3 ml-3">
										<div class="row d-flex flex-column">
											<div  class="col mb-2">
												<p style="position: absolute; top: -10px; right: 10px;"><?php echo date('d.m.Y', strtotime($m[create_date]));?></p>
												<a class="text-decoration-none" href="/template.php?id=<?php echo $m[id];?>"><?php echo trim($m[name]);?></a>
											</div>
											<div style="height: 25px;" class="col">
												<p >Тип: <a class="text-decoration-none" href="/view.php?type=<?php echo $m[type];?>"><?php echo $m[type];?></a></p>
											</div>
											<div  style="height: 25px;" class="col">
												<p>Средняя оценка: <?php echo (empty($m[avg_mark]) ? '0':$m[avg_mark])?></p>
											</div>
											<div style="height: 25px;" class="col">
												<p>Количество заказов: <?php echo (empty($m[count_orders]) ? '0':$m[count_orders])?></p>
											</div>
											<div style="height: 25px;" class="col">
												<p>Количество комментариев: <?php echo (empty($m[count_comments]) ? '0':$m[count_orders])?></p>
											</div>
											<div style="height: 25px;" class="col">
												<p>Страна: <a class="text-decoration-none" href="/view.php?country=<?php echo $m[country]?>"><?php echo $m[country]?></a></p>
											</div>
											<div style="height: 25px;" class="col">
												<?php $tags = R::getAll('SELECT id_tag FROM tags_list WHERE id_multimedia = ?', [$m[id]]);
													if(!empty($tags)):
												?>
															<p>Теги: 
															<?php 
																foreach ($tags as $t): ?>
																	<a class="text-decoration-none" href="/view.php?tag=<?php $name = R::findOne('tags', 'id = ?', [$t[id_tag]]);
																														echo $name->name;?>">
																														<?php 
																														echo $name->name;?></a> 
															<?php endforeach;?>
															</p>
												<?php endif;?>
											</div>
										</div>																				
									</div>
								</div>
							<?php endforeach;?>
						</div>
					</div>
				</div>
				<div style="max-width: 250px;" class="col p-3"></div>
			</div>
				<?php endif;?>
		</div>
		
	<?php require "blocks/footer.php";?>
</body>
</html>
<?php endif;
	else:
		header('Location: /');
		endif;?>