<?php session_start(); 
if(!isset($_SESSION['id'])):
	header('Location: /');
	exit();
else:
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Настройки</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/styles.css">
	<script src="css/jquery-3.2.1.slim.min.js"></script>
	<script src="css/popper.min.js"></script>
	<script src="css/bootstrap.min.js"></script>
	<style>
		input[type='number'] {
		    -moz-appearance:textfield !important;
		}

		input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		    -webkit-appearance: none !important;
		}
	</style>
</head>
<body>
	<?php require "blocks/header.php";?>
	<div class="container">
		<div class="row d-flex flex-row mt-5">
			<div class="col-3">
				<div class="row d-flex flex-column">
					<a class="btn btn-outline-light" href="/settings.php">Настройки</a>
					<a class="btn btn-outline-light" href="/settings.php?type=order_list">Список заказов</a>
					<a class="btn btn-outline-light" href="/settings.php?type=sell">Продать мультимедиа</a>
					<a class="btn btn-outline-light" href="/settings.php?type=balance">Пополнить баланс</a>
					<a class="btn btn-outline-light" href="/settings.php?type=multimedia">Мои мультимедиа</a>
				</div>
			</div>
			<div class="col">
				<div class="row d-flex flex-column">
					<?php if($_GET['type'] == 'order_list'):
								if(isset($_GET['id'])):
									if(empty($_GET['id'])):
										?>
										<h3 class="ml-3">Упс, что то пошло не так</h3>
									<? else: $check = R::findOne('orders', 'id = ?', [$_GET['id']]);
									if(!($check->id_client == $_SESSION['id'])):
										echo '<h3 class="ml-3">Упс, что то пошло не так</h3>';
									else:
										if($check):
											$multimedia = R::getAll('SELECT * FROM multimedia_list WHERE id_order = ?', [$_GET['id']]);
											foreach ($multimedia as $m):
												$mult = R::findOne('multimedia', 'id = ?', [$m[id_multimedia]]);?>
												<div class="row ml-3 d-flex flex-column">
													<div class="col">
														<div class="row">
															<div class="col">
																<a href="/template.php?id=<?echo $mult->id;?>"><?echo $mult->name;?></a>
															</div>
															<div class="col">
																Цена: <?php echo $mult->price." UAH"?>
															</div>
														</div>
													</div>
													<div class="col">
														<form action="scripts/mark.php"  class="d-flex flex-row"method="POST">
															<select class="form-control" style="width: auto;" name="value">
																<?for($i = 5; $i >= 1; $i--):?>
																<option value="<? echo $i;?>" <?$mark = R::findOne('mark', 'id_client = ? AND id_multimedia = ?', [$_SESSION['id'], $mult->id]);
																if($mark->value == $i) echo 'selected';?>><? echo $i;?></option>
																<?endfor;?>
															</select>
															<button class="btn-outline-warning btn ml-3" name='<?echo $mult->id;?>'>Оценить</button>
															<input hidden name="<? echo $_GET['id'];?>">
															<a style="position: absolute; left: 460px; bottom: 20px;" class="btn btn-outline-success" download href="media/<?echo $mult->type.'/'.$mult->id.'.zip';?>">Скачать</a>
														</form>
													</div>													
												</div>									
												
									<?php endforeach; endif; endif; endif; else:$order_list = R::getAll('SELECT * from orders WHERE id_client = ? AND state = \'paid\'', [$_SESSION['id']]);
										if(!empty($order_list)):
											foreach($order_list as $ol):?>
												<div class="row d-flex flex ml-3">
													<div class="col">
														<a href="/settings.php?type=order_list&id=<?php echo $ol[id];?>">#<?php echo $ol[id];?></a>
														<?php $price = R::getAll('SELECT SUM(multimedia.price) FROM multimedia JOIN multimedia_list ON multimedia.id = multimedia_list.id_multimedia WHERE id_order = ?', [$ol[id]]);
														echo 'В сумме получилось '. $price[0][sum] . ' UAH';?>
														<p>Время заказа: <? echo date('H:i d.m.Y', strtotime($ol[date]))?></p>
													</div>
												</div>
											<?php endforeach;?>				

							<?php else:?>
								<h3 class="ml-3">У вас еще нет покупок</h3>
							<?php endif; endif;?>
					<?php elseif($_GET['type'] == 'sell'):?>
						<form action="scripts/multimedia.php" class="d-flex flex-column ml-3" style="width: 300px" method="POST" enctype="multipart/form-data">
							Выберите файл для обложки:<input class="form-control-file" name="cover" type="file">
							Введите название:<input type="text" name="name" class="mt-2 form-control" required>
							Введите описание:<textarea name="text"  style="width: 100%;  height: 100px;" class="form-control mt-2 p-3" required></textarea>
							Выберите тип:<select name="type" required class="mt-2 form-control">
								<option></option>
								<option value="video">Video</option>
								<option value="photo">Photo</option>
								<option value="audio">Audio</option>
							</select>
							Введите теги:<input type="text" name="tags" class="mt-2 form-control" placeholder="Через пробел, максимум 5 штук">
							<?php $countries = ["Абхазия", "Австралия", "Австрия", "Азербайджан", "Албания", "Алжир", "Ангола", "Ангуилья", "Андорра", "Антигуа и Барбуда", "Антильские острова", "Аргентина", "Армения", "Афганистан", "Багамские острова", "Бангладеш", "Барбадос", "Бахрейн", "Беларусь", "Белиз", "Бельгия", "Бенин", "Бермуды", "Болгария", "Боливия", "Босния/Герцеговина", "Ботсвана", "Бразилия", "Британские Виргинские о-ва", "Бруней", "Буркина Фасо", "Бурунди", "Бутан", "Вануату", "Ватикан", "Великобритания", "Венгрия", "Венесуэла", "Вьетнам", "Габон", "Гаити", "Гайана", "Гамбия", "Гана", "Гваделупа", "Гватемала", "Гвинея", "Гвинея-Бисау", "Германия", "Гернси остров", "Гибралтар", "Гондурас", "Гонконг", "Государство Палестина", "Гренада", "Гренландия", "Греция", "Грузия", "ДР Конго", "Дания", "Джерси остров", "Джибути", "Доминиканская Республика", "Египет", "Замбия", "Западная Сахара", "Зимбабве", "Израиль", "Индия", "Индонезия", "Иордания", "Ирак", "Иран", "Ирландия", "Исландия", "Испания", "Италия", "Йемен", "Кабо-Верде", "Казахстан", "Камбоджа", "Камерун", "Канада", "Катар", "Кения", "Кипр", "Китай", "Колумбия", "Коста-Рика", "Кот-д'Ивуар", "Куба", "Кувейт", "Кука острова", "Кыргызстан", "Лаос", "Латвия", "Лесото", "Либерия", "Ливан", "Ливия", "Литва", "Лихтенштейн", "Люксембург", "Маврикий", "Мавритания", "Мадагаскар", "Македония", "Малайзия", "Мали", "Мальдивские острова", "Мальта", "Марокко", "Мексика", "Мозамбик", "Молдова", "Монако", "Монголия", "Мьянма (Бирма)", "Мэн о-в", "Намибия", "Непал", "Нигер", "Нигерия", "Нидерланды (Голландия)", "Никарагуа", "Новая Зеландия", "Новая Каледония", "Норвегия", "О.А.Э.", "Оман", "Пакистан", "Палау", "Панама", "Папуа Новая Гвинея", "Парагвай", "Перу", "Питкэрн остров", "Польша", "Португалия", "Пуэрто Рико", "Республика Конго", "Реюньон", "Россия", "Руанда", "Румыния", "США", "Сальвадор", "Самоа", "Сан-Марино", "Сан-Томе и Принсипи", "Саудовская Аравия", "Свазиленд", "Святая Люсия", "Северная Корея", "Сейшеллы", "Сен-Пьер и Микелон", "Сенегал", "Сент Китс и Невис", "Сент-Винсент и Гренадины", "Сербия", "Сингапур", "Сирия", "Словакия", "Словения", "Соломоновы острова", "Сомали", "Судан", "Суринам", "Сьерра-Леоне", "Таджикистан", "Таиланд", "Тайвань", "Танзания", "Того", "Токелау острова", "Тонга", "Тринидад и Тобаго", "Тувалу", "Тунис", "Туркменистан", "Туркс и Кейкос", "Турция", "Уганда", "Узбекистан", "Украина", "Уоллис и Футуна острова", "Уругвай", "Фарерские острова", "Фиджи", "Филиппины", "Финляндия", "Франция", "Французская Полинезия", "Хорватия", "Чад", "Черногория", "Чехия", "Чили", "Швейцария", "Швеция", "Шри-Ланка", "Эквадор", "Экваториальная Гвинея", "Эритрея", "Эстония", "Эфиопия", "ЮАР", "Южная Корея", "Южная Осетия", "Ямайка", "Япония"];
							?>     
        					Выберите страну:<select class="form-control mt-2" name="country" required>
        						<option></option>
							<?php foreach($countries as $country): ?>				
								<option value="<?php echo $country?>"><?php echo $country?></option>
							<?php endforeach;?>
							</select>
							Введите цену:<input min="0" max="10000" type="number" name="price" step="0.01" class="mt-2 form-control">
							Выберите файл мультимеда:<input class="form-control-file mt-2" name="multimedia[]" type="file" required multiple>
							<button class="mt-2 btn btn-outline-success">Отправить на проверку</button>
						</form>
					<?php elseif($_GET['type'] == 'balance'):?>
						<!-- <div class="row d-flex flex-column"
							 <div class="col"> -->
								<div class="row d-flex flex-row ml-3 mt-2">
									<div class="col">
										<h5>Баланс: <?php echo $user->balance;?> UAH</h5>
									</div>
									<div class="col-3">
										<form action="scripts/balance.php" method="POST">
											<button name="up" class="btn btn-outline-success">Пополнить баланс</button>
										</form>
									</div>
									<div class="col-3">
										<form action="scripts/balance.php" method="POST">
											<button name="down" class="btn btn-outline-danger">Снять баланс</button>
										</form>
									</div>
								</div>
							<!-- </div> 
								 <div class="col">
								 	История баланса: 
								</div>
						</div> -->
					<?php elseif($_GET['type'] == 'multimedia'):?>
						<?php $multi = R::getAll('SELECT * FROM multimedia WHERE id_client = ? ORDER BY create_date DESC', [$_SESSION['id']]);
								if(!$multi):?>
									<h3 class="ml-3">У вас еще нет мультимедиа</h3>
								<?php else:?>
									<?php if(isset($_GET['id'])):?>
										<?php if(empty($_GET['id'])):?>
											<h3 class="ml-3">Упс, что то пошло не так</h3>
										<?php else:
											$multim = R::findOne('multimedia', 'id = ?', [$_GET['id']]);
												if($multim):
													if($multim->id_client == $_SESSION['id']):?>
														<div class="row d-flex flex-column ml-3">
															<div class="col d-flex flex-row">
																<a class="btn  btn-outline-danger" href="/settings.php?type=multimedia&id=<?echo $_GET['id']?>">Редактировать</a>
																<a class="btn ml-2 btn-outline-success" href="/settings.php?type=multimedia&id=<?echo $_GET['id']?>&stats=t">Статистика</a>			
															</div>
															<?php if($_GET['stats'] == 't'):?>
																<div class="col mt-2">
																	<form action="settings.php">
																		<div class="d-flex flex-row">
																			<input type="text" hidden name="type" value="multimedia">
																			<input type="text" hidden name="id" value="<?echo $_GET['id']?>">
																			<input type="text" hidden name="stats" value="t">
																			<input type="date" class="form-control" name="date1" style="width: auto;" required>
																			<h6> ____ </h6>
																			<input type="date" class="form-control" name="date2" style="width: auto;" required>
																			<button class="ml-2 btn btn-outline-info">Посмотреть</button>
																		</div>

																	</form>																	
																</div>
																<div class="col">
																	<?php if(isset($_GET['date1']) and isset($_GET['date2'])):?>
																		<?php if($_GET['date2']>=$_GET['date1']):?>
																			<?php $stats = R::getAll('SELECT * FROM statistics(?, ?, ?)', [$_GET['date1'], $_GET['date2'], $_GET['id']]);?>
																			<?php if($stats AND !empty($stats[0][sum])):?>
																				<h5 class="mt-2">Вы продали: <?echo $stats[0][count];?></h5>
																				<h5>На сумму <?echo $stats[0][sum]?> UAH</h5>
																			<?php else:?>
																				<h5 class="mt-2">К сожалению ничего нет</h5>
																			<?php endif;?>
																		<? else: ?>
																			<h5 class="mt-2">Введите корректную дату</h5>
																		<?php endif;?>
																	<?php endif;?>
																</div>
															<?php else: ?>
																<div class="col mt-2">
																	<div class="row d-flex flex-row">
																			<div class="col">
																				<img style="width: 130px; height: 170px; object-fit: cover;"src="media/cover/<?php echo $multim->cover;?>"> 
																			</div>
																			<div class="col mt-1">
																				<form action="scripts/cover.php" method="POST" enctype="multipart/form-data">
																					<input class="form-control-file" name="cover" type="file" required>
																					<button name="<?echo $_GET['id'];?>" class="btn btn-outline-info mt-2">Сменить обложку</button>
																				</form>
																			</div>
																		</div>
																	</div>		
																	<div class="col mt-2" style="width: 300px;">
																		<form action="scripts/edit_multimedia.php" method="POST">
																			Название:<input class="form-control mt-2" name="name"  type="text" value="<?php echo $multim->name?>">
																			Описание:<textarea name="text"  style="width: 100%;  height: 100px;" class="form-control mt-2 p-3"><?php echo $multim->description?></textarea>
																			Цена:<input min="0" max="10000" type="number" name="price" step="0.01" class="mt-2 form-control" value="<?php echo $multim->price?>">
																			<button name="<?echo $_GET['id'];?>" class="btn btn-outline-success mt-2">Сохранить</button>
																		</form>
																	</div>	
																<?php endif;?>													
															</div>
														</div>
													<?php else:?>
														<h3 class="ml-3">Упс, что то пошло не так</h3>
													<?php endif;?>
												<?php else:?>
													<h3 class="ml-3">Упс, что то пошло не так</h3>
											<?php endif;?>
										<?php endif;?>
									<?php else:?>
										<div class="row ml-3 d-flex flex-column">
											<?php foreach($multi as $m):?>
												<div class="col mt-2">
													<div class="row d-flex flex-row">
														<div style="max-width: 130px;"class="col d-flex flex-column p-3">
															<a href="/template.php?id=<?php echo $m[id];?>"><img style="width: 130px; height: 170px; object-fit: cover;"src="media/cover/<?php 
																					if(empty($m[cover])) {
																						echo 'default.jpg';
																					}
																					else {
																						echo $m[cover];
																					}
																?>">

															</a>
															<p class="text-nowrap text-center ml-4 mb-0"><?php echo $m[price]?> UAH</p>
														</div>
														<div class="col d-flex flex-column p-3 ml-3">
															<div class="row d-flex flex-column">
																<div  class="col mb-2">
																	<p style="position: absolute; top: -10px; right: 10px;"><?php echo date('d.m.Y', strtotime($m[create_date]));?></p>
																	<a class="text-decoration-none <?if(empty($m[id_administrator])) echo 'text-danger'; else echo 'text-success';?>" href="/template.php?id=<?php echo $m[id];?>"><?php echo trim($m[name]);?></a>
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
																<a style="position: absolute; top: 40px; right: 0;" class="btn btn-outline-success" href="/settings.php?type=multimedia&id=<?php echo $m[id];?>">Редактировать</a>
															</div>																				
														</div>
													</div>

												</div>

											<?php endforeach;?>
										</div>
									<?php endif;?>
								<?php endif;?>
					<?php else:?>
							<div class="col">
								<?php $user = R::findOne('clients', "id = ?", [$_SESSION['id']]);
								if($user->email_verified == false):?>
									<form action="scripts/email_verified.php" method="POST">
										<button class="btn btn-outline-primary mb-2">Подтвердить почту</button>
									</form>
								<?php endif; ?>
								</div>
							<div class="col">
							<div class="row d-flex flex-row">
								<div class="col">
									<p>Загрузите свою аватарку</p>
									<img style="width: 130px; height: 170px; object-fit: cover;"src="media/avatar/<?php echo $user->avatar;?>"> 
								</div>
								<div class="col mt-5">
									<form action="scripts/avatar.php" method="POST" enctype="multipart/form-data">
										<input class="form-control-file" name="avatar" type="file">
										<button class="btn btn-outline-info mt-2">Загрузить аватар</button>
									</form>
								</div>
							</div>
						</div>
						<div class="col mt-2">
							<form action="scripts/edit_email.php" method="POST" style="max-width: 250px;" class="d-flex flex-column" >
								<input type="email" class="form-control" name="email" placeholder="Введите новую почту" >
								<button class="btn btn-outline-success mt-2">Сменить почту</button>
							</form>
						</div>
						<div class="col mt-2 ">
							<form action="scripts/edit_pass.php" method="POST" class="d-flex flex-column" style="max-width: 250px;">
								<input type="password" class="form-control mt-1" name="old_p" placeholder="Введите старый пароль">
								<input type="password" class="form-control mt-1" name="new_password" placeholder="Введите новый пароль">
								<input type="password" class="form-control mt-1" name="new_password2" placeholder="Повторите новый пароль">
								<button class="btn btn-outline-danger mt-2">Сменить пароль</button>
							</form>
						</div>

					<?php endif;?>
				</div>
			</div>
		</div>
	</div>
	 <?php if(!empty($_SESSION['errors_settings'])):?>
					    	<!--Если есть ошибки -->
			<div style = "width: 100%;  position:absolute; bottom:0;" class="row justify-content-end">
				<div class="alert alert-danger alert-dismissible show  p-3" id="close">
					<h6 class="alert-heading">Ошибка</h6>
					<button type="button" class="close p-2" id="close_a">
						<span>&times;</span>
					</button>
	 				<hr>
	 				<?php if(count($_SESSION['errors_settings']) > 1):?>
	  					<ul>
	  						<?php foreach($_SESSION['errors_settings'] as $e) echo"<li>$e</li>";?>
	  					</ul>
	  				<?php else:
						echo $_SESSION['errors_settings'][0];?>
		 			<?php endif;?>
					</div>
			</div>
			<script>
				document.getElementById('close_a').onclick = function() {
				document.getElementById('close').hidden = true;
				}
			</script>
	<?php unset($_SESSION['errors_settings']); endif;?> 
	<?php require "blocks/footer.php";?>
</body>
</html>
<?php endif;?>

