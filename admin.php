<?php session_start(); 
	require_once "./add/db.php";
	$user = R::findOne('clients', "id = ?", [$_SESSION['id']]);
	if(!isset($_SESSION['id']) or $user->rights == 'user'){
		header('Location: /');
		exit();
	}?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Админ панель</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	
</head>
<body>
	<?php require "blocks/header.php";?>
	<div class="container">
		<div class="row d-flex flex-row mt-5">
			<div class="col-3">
				<div class="row d-flex flex-column">
					<a class="btn btn-outline-light" href="/admin.php">Пользователи</a>
					<a class="btn btn-outline-light" href="/admin.php?type=multimedia">Проверка мультимедиа</a>
					<?if($user->rights == 'creator'):?>
						<a class="btn btn-outline-light" href="/admin.php?type=log">Журнал действий</a>
					<?endif;?>
				</div>
			</div>
			<div class="col">
				<div class="row d-flex flex-column">
					<?php if($_GET['type'] == 'multimedia'):?>
						<div class="col">
							<? $multimedia = R::getAll('SELECT * FROM w8_multimedia');
								foreach ($multimedia as $m):?> 
									<?php if($m[id_client] != $_SESSION['id']):?>
										<div class="row d-flex flex-row mt-2">
											<div class="col">
												<a href="/template.php?id=<?echo $m[id]?>"><?echo $m[id]?></a>
											</div>
											<div class="col">
												<a href="/template.php?id=<?echo $m[id]?>"><?echo $m[name]?></a>
											</div>
											<div class="col">
												<a href="/profile.php?id=<?echo $m[id_client]?>"><?echo R::findOne('clients', 'id = ?', [$m[id_client]])->login;?></a>
											</div>
											<div class="col">
												<p><?echo date('d.m.Y', strtotime($m[create_date]))?></p>
											</div>
										</div>
									<?php endif;?>
								<?php endforeach;?>
						</div>
					<?php elseif($_GET['type'] == 'log' and $user->rights == 'creator'):?>
						<div class="row d-flex flex-column ml-3 mt-2">
							<div class="col">
								<form class="d-flex flex-row" action="admin.php?type=log" method="GET">
									<input type="text" hidden name='type' value="log">
									<select class="form-control" style="width: auto;" name="role" required>
										<option value="all">Все</option>
										<?php $r = R::getAll('SELECT DISTINCT role FROM admins_log');
										foreach($r as $role):?>
											<option value="<?echo $role[role]?>"><?echo $role[role]?></option>
										<?php endforeach;?>
									</select>
									<button class="btn btn-outline-info ml-2">Поиск</button>
								</form>
							</div>
							<? $logs = R::getAll('SELECT * FROM admins_log'.((isset($_GET['role']) and $_GET['role'] != 'all') ? (' WHERE role = \''.$_GET[role].'\' ') : ' ').'ORDER BY date DESC');
							foreach($logs as $l): ?>
								<div class="col d-flex flex-row mt-1">
									<div class="col-3">
										<a href="/profile.php?id=<?$u = R::findOne('clients', 'login = ?', [$l[login]]);echo $u->id;?>"><?echo $l[login]?></a>
									</div>
									<div class="col">
										<p><?
										$action = '';
										$tablen = '';
										if($l[action] == 'DELETE'){
											$action = 'удалил';
										}
										elseif($l[action] == 'INSERT'){
											$action = 'добавил';
										}
										else{
											$action = 'изменил';
										}
										if($l[tablen] == 'clients'){
											$tablen = 'клиента';
										}
										elseif($l[tablen] == 'multimedia'){
											$tablen = 'мультимедиа';
										}
										else{
											$tablen = 'комментарий';
										}
										echo '  '.$action.' '.$tablen.' с ID '.$l[id].' '.date('d.m.Y H:m', strtotime($l[date]));
										?></p>		
									</div>
														
								</div>
							<?endforeach;?>
						</div>
					<?php else:?>
						<div class="col">
							<div class="row d-flex flex-column">
								<div class="col">
									<form action="admin.php" class="d-flex flex-row" method="GET">
										<input class="form-control" style="max-width: 250px;" type="text" name="login">
										<button class="ml-2 btn btn-outline-success">Найти</button>
									</form>
								</div>
								<div class="col">
									<?php 
										$query = empty($_GET['login']) ? ' ' : ' WHERE login ilike ?';
										$object = [];
										if(!empty($_GET['login'])){
												$object[] = "%".$_GET['login']."%";
											}
										$users = R::getAll('SELECT * FROM clients'. $query .'ORDER BY last_t', $object); 
										foreach($users as $u):?>
											<?php if($u[id] != $_SESSION['id']):?>
											<div class="row d-flex flex-column mt-4">
												<div class="col">
													<div class="row d-flex flex-row">
														<div class="col-1">
															<a href="/profile.php?id=<?echo $u[id]?>" class="text-decoration-none"><?php echo $u[id];?></a>
														</div>
														<div class="col-3">
															<a href="/profile.php?id=<?echo $u[id]?>" style="word-wrap: break-word;" class="text-decoration-none"><?php echo $u[login];?></a>
														</div>
														<div class="col-3" style="word-wrap: break-word;">
															<a href="/profile.php?id=<?echo $u[id]?>" class="text-decoration-none"><?php echo $u[email];?></a>
														</div>														
														<?php if($user->rights == 'creator'):?>
															<div class="col">
																<form action="scripts/change_user.php" method="POST" class="d-flex flex-row">
																	<select style="width: auto;" class="form-control" name="role">
																		<?php $role=['user', 'admin', 'creator'];
																		foreach($role as $r):?>
																			<option <?echo $r == $u[rights] ? 'selected' : '';?> value="<?echo $r?>"><?echo $r?></option>
																		<?php endforeach;?>
																	</select>
																	<input hidden name="id" value="<? echo $u[id]?>">
																	<button name="change" class="ml-2 btn btn-outline-warning">Изменить</button>
																	<button name="delete" class="ml-2 btn btn-outline-danger">Удалить</button>
																</form>
															</div>
														<?php else:?>
															<div class="col-2">
																<p><?php echo $u[rights];?></p>
															</div>
														<?php endif;?>
													</div>
												</div>
											</div>											
											<?php endif;?>
										<?php endforeach;?>
								</div>
							</div>
						</div>
					<?php endif;?>
					
				</div>
				<p style="position: absolute; top: -30px; right: 0;">Всего мы заработали: <?php $money = R::getAll('SELECT SUM(multimedia.price*?) FROM multimedia_list JOIN orders ON orders.id = multimedia_list.id_order JOIN multimedia ON multimedia.id = multimedia_list.id_multimedia WHERE orders.state = \'paid\'', [$commission]); echo $money[0][sum];?> UAH</p>
			</div>
		</div>
	</div>
	<?php require "blocks/footer.php" ?>
</body>
</html>