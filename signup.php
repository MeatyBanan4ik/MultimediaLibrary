<?php session_start();
if(isset($_SESSION['id'])){
  header('Location: /');
}?>
<!DOCTYPE html>
<html style="height: 100%; width: 100%;">
<head>
  <meta charset="utf-8">
  <title>Регистрация</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <script src="css/jquery-3.2.1.slim.min.js"></script>
  <script src="css/popper.min.js"></script>
  <script src="css/bootstrap.min.js"></script>
  
</head>
<body style="height: 100%; width: 100%;">
  <?php require_once "blocks/header.php";?>   
  <div style="display: flex; align-items: center; height: 60%;">
    <div class="p-3 border border-secondary box-shadow" style="max-width: 500px; margin: auto; width: 100%; margin-bottom: 0px;">
        <form action="scripts/register.php" method="POST">
        <p class="mb-1">Введите ваш e-mail:</p>
        <input type="email" name="email" placeholder="E-mail" maxlength="50" class="form-control <?php if (in_array(2, $_SESSION['errors_sn'])) echo 'is-invalid';?>" value="<?php echo @$_SESSION['data_s'][0];?>"  required>
        <br/>
        <p class="mb-1">Введите ваш логин:</p>
        <input type="text" name="login" placeholder="Логин" maxlength="50" class="form-control <?php if (in_array(1, $_SESSION['errors_sn'])) echo 'is-invalid';?>" value="<?php echo @$_SESSION['data_s'][1];?>" required>
        <br/>
        <p class="mb-1">Выберите страну:</p> 
        <?php 
			$countries = ["Абхазия", "Австралия", "Австрия", "Азербайджан", "Албания", "Алжир", "Ангола", "Ангуилья", "Андорра", "Антигуа и Барбуда", "Антильские острова", "Аргентина", "Армения", "Афганистан", "Багамские острова", "Бангладеш", "Барбадос", "Бахрейн", "Беларусь", "Белиз", "Бельгия", "Бенин", "Бермуды", "Болгария", "Боливия", "Босния/Герцеговина", "Ботсвана", "Бразилия", "Британские Виргинские о-ва", "Бруней", "Буркина Фасо", "Бурунди", "Бутан", "Вануату", "Ватикан", "Великобритания", "Венгрия", "Венесуэла", "Вьетнам", "Габон", "Гаити", "Гайана", "Гамбия", "Гана", "Гваделупа", "Гватемала", "Гвинея", "Гвинея-Бисау", "Германия", "Гернси остров", "Гибралтар", "Гондурас", "Гонконг", "Государство Палестина", "Гренада", "Гренландия", "Греция", "Грузия", "ДР Конго", "Дания", "Джерси остров", "Джибути", "Доминиканская Республика", "Египет", "Замбия", "Западная Сахара", "Зимбабве", "Израиль", "Индия", "Индонезия", "Иордания", "Ирак", "Иран", "Ирландия", "Исландия", "Испания", "Италия", "Йемен", "Кабо-Верде", "Казахстан", "Камбоджа", "Камерун", "Канада", "Катар", "Кения", "Кипр", "Китай", "Колумбия", "Коста-Рика", "Кот-д'Ивуар", "Куба", "Кувейт", "Кука острова", "Кыргызстан", "Лаос", "Латвия", "Лесото", "Либерия", "Ливан", "Ливия", "Литва", "Лихтенштейн", "Люксембург", "Маврикий", "Мавритания", "Мадагаскар", "Македония", "Малайзия", "Мали", "Мальдивские острова", "Мальта", "Марокко", "Мексика", "Мозамбик", "Молдова", "Монако", "Монголия", "Мьянма (Бирма)", "Мэн о-в", "Намибия", "Непал", "Нигер", "Нигерия", "Нидерланды (Голландия)", "Никарагуа", "Новая Зеландия", "Новая Каледония", "Норвегия", "О.А.Э.", "Оман", "Пакистан", "Палау", "Панама", "Папуа Новая Гвинея", "Парагвай", "Перу", "Питкэрн остров", "Польша", "Португалия", "Пуэрто Рико", "Республика Конго", "Реюньон", "Россия", "Руанда", "Румыния", "США", "Сальвадор", "Самоа", "Сан-Марино", "Сан-Томе и Принсипи", "Саудовская Аравия", "Свазиленд", "Святая Люсия", "Северная Корея", "Сейшеллы", "Сен-Пьер и Микелон", "Сенегал", "Сент Китс и Невис", "Сент-Винсент и Гренадины", "Сербия", "Сингапур", "Сирия", "Словакия", "Словения", "Соломоновы острова", "Сомали", "Судан", "Суринам", "Сьерра-Леоне", "Таджикистан", "Таиланд", "Тайвань", "Танзания", "Того", "Токелау острова", "Тонга", "Тринидад и Тобаго", "Тувалу", "Тунис", "Туркменистан", "Туркс и Кейкос", "Турция", "Уганда", "Узбекистан", "Украина", "Уоллис и Футуна острова", "Уругвай", "Фарерские острова", "Фиджи", "Филиппины", "Финляндия", "Франция", "Французская Полинезия", "Хорватия", "Чад", "Черногория", "Чехия", "Чили", "Швейцария", "Швеция", "Шри-Ланка", "Эквадор", "Экваториальная Гвинея", "Эритрея", "Эстония", "Эфиопия", "ЮАР", "Южная Корея", "Южная Осетия", "Ямайка", "Япония"];
		?>     
        <select class="form-control" name="country" id="country" required>
        	<option></option>
			<?php foreach($countries as $country): ?>				
				<option value="<?php echo $country?>"><?php echo $country?></option>
			<?php endforeach; ?>
		</select>
        <br/>
        <p class="mb-1">Введите ваш пароль:</p>
        <input type="password" name="password" placeholder="Пароль" class="form-control <?php if (preg_grep("/[3-4]/", $_SESSION['errors_sn'])) echo 'is-invalid';?>" required maxlength="100">
        <br/>
        <p class="mb-1">Введите ваш пароль еще раз:</p>
        <input type="password" name="password2" maxlength="30" placeholder="Повторите пароль" class="form-control <?php if (in_array(4, $_SESSION['errors_sn'])) echo 'is-invalid';?>" required>
        <br/>
        <button type="submit" name="sign_up" class="btn btn-outline-info" autofocus>Зарегистрироваться</button>
      </form>
      </div> 
    </div>
    <?php if(!empty($_SESSION['errors_sm'])):?>
    	<!--Если есть ошибки -->
      	<div style = "width: 100%;  position:absolute; bottom:0;" class="row justify-content-end">
      		<div class="alert alert-danger alert-dismissible show  p-3" id="close">
      			<h6 class="alert-heading">Ошибка</h6>
      			<button type="button" class="close p-2" id="close_a">
    				<span>&times;</span>
 				</button>
      			<hr>
		  		<ul>
		  			<?php foreach($_SESSION['errors_sm'] as $e) echo"<li>$e</li>";?>
		  		</ul>
	  		</div>
  		</div>
  		<script>
  		 document.getElementById('close_a').onclick = function() {
      		document.getElementById('close').hidden = true;
  			}
		</script>
  	<?php unset($_SESSION['errors_sm']); unset($_SESSION['errors_sn']); unset($_SESSION['data_s']); endif;?> 
  	
  	
  <?php require_once "blocks/footer.php"?>
</body>
</html>

