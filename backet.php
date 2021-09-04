<?php session_start(); 
require_once "./add/db.php"; 
if(!isset($_SESSION['id'])):
	header('Location: /');
else:
	?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<title>Корзина</title>
	<script src="css/jquery-3.2.1.slim.min.js"></script>
	<script src="css/popper.min.js"></script>
	<script src="css/bootstrap.min.js"></script>
</head>
<body>
	<?php require "blocks/header.php";?>
	<div class="container">
		<div class="row d-flex flex-row">
			<div class="col">
				<div class="row d-flex flex-column">
					<div class="col mt-2">
						<h5>Корзина:</h5>
					</div>
					<div class="col">
						<div class="row d-flex flex-column">
							<?php 
							$check = R::findOne('orders', 'id_client = ? AND state = \'backet\'', [$_SESSION['id']]);
							if($check):
								$product = R::getAll('SELECT * FROM multimedia_list WHERE id_order = ?', [$check->id]);
								foreach ($product as $p):
									$m = R::findOne('multimedia', 'id = ?', [$p[id_multimedia]]);					
								?>
								<div class="col mt-2">
									<div class="row d-flex p-2 flex-row">
										<div class="col-1">
											<a href="/template.php?id=<?php echo $m->id;?>"><img style="width: 50px; height: 70px; object-fit: cover;" src="media\cover\<?php if (empty($m->cover)) {
																																												echo 'default.jpg';
																																											}
																																							else {
																																								echo $m->cover;
																																							}?>"></a>
										</div>
										<div class="col">
											<div class="row d-flex flex-column">
												<div class="col">
													<div class="row d-flex flex-row">
														<div class="col ml-3">
															<a class="text-decoration-none" href="/template.php?id=<?php echo $m->id;?>"><?php echo $m->name;?></a>
														</div>
														<div class="col">
															<p class="text-right"><?php echo $m->price.' UAH';?></p>
														</div>
													</div>
												</div>
												<div class="col ml-3">
													<form action="scripts/from_backet.php" method="POST">
														<button style="position: absolute; top: -7px;" class="btn btn-outline-danger" name="<?php echo $m->id;?>">Удалить</button>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
						<?php endforeach;
							endif?>
						</div>						
					</div>
				</div>
			</div>
			<div class="col">
				<div class="card text-white bg-dark mt-3">
					<div class="card-body">
						<h5 class="card-title">Всего к оплате:</h5>
						<p class="card-text text-right"><?php 
							$sum = R::getAll('SELECT round(SUM(multimedia.price)::numeric, 2) FROM multimedia JOIN multimedia_list ON multimedia.id =  multimedia_list.id_multimedia WHERE id_order = ?', [$check->id]);
							if($sum[0][round]) {
								echo $sum[0][round] . ' UAH';
							}
							else {
								echo '0 UAH';
							}
						?></p>
						<form action="scripts/pay.php" method="POST">
							<button name="<?php echo $check->id;?>" class="btn btn-outline-light">Оплатить</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	 <?php if(!empty($_SESSION['backet'])):?>
    	<!--Если есть ошибки -->
      	<div style = "width: 100%;  position:absolute; bottom:0;" class="row justify-content-end">
      		<div class="alert alert-danger alert-dismissible show  p-3" id="close">
      			<h6 class="alert-heading">Ошибка</h6>
      			<button type="button" class="close p-2" id="close_a">
    				<span>&times;</span>
 				</button>
      			<hr>

		  			<?php echo $_SESSION["backet"];?>

	  		</div>
  		</div>
  		<script>
  		 document.getElementById('close_a').onclick = function() {
      		document.getElementById('close').hidden = true;
  			}
		</script>
  	<?php unset($_SESSION['backet']); endif;?> 
	<?php require "blocks/footer.php";?>
</body>
</html>
<?php endif;?>