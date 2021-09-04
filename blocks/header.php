<?php session_start(); require_once "./add/db.php";?>
<header class="p-3">
	<div class="row align-items-center">
		<div class="col ml-5">
			<a class="text-decoration-none text-white-50 h4 p-2 font-weight-bold" href="/">Главная</a>
			<a class="text-decoration-none text-white-50 h4 p-2 font-weight-bold" href="/view.php?type=photo">Фото</a>
			<a class="text-decoration-none text-white-50 h4 p-2 font-weight-bold" href="/view.php?type=video">Видео</a>
			<a class="text-decoration-none text-white-50 h4 p-2 font-weight-bold" href="/view.php?type=audio">Аудио</a>
		</div>
		
			<?php 
				if(!isset($_SESSION['id'])):
			?>
				<div class="col-auto mr-5">
					<a class="btn btn-outline-light sign" href="auth.php">Войти</a>
				</div>
			<?php else:?>
				<div class="dropdown col-auto mr-5 media">
					<a role="button" class="d-flex text-decoration-none text-light align-self-start " id="dropdownMenuLink" data-toggle="dropdown">
						<p style="margin-top: 7px; margin-right: 10px; margin-bottom: -5px; color: LightGray;" class="">Баланс: <?php $user = R::findOne('clients', "id = ?", [$_SESSION['id']]); echo round($user->balance, 2, PHP_ROUND_HALF_UP)?></p>
						<img class="rounded-circle align-self-start" style="width: 38px;height: 38px; object-fit: cover;" src="<?php 
							echo "../media/avatar/". $user->avatar;
						?>"></img>

						
					</a>
					 <div class="dropdown-menu">
    					<a class="dropdown-item" href="/profile.php?id=<?php echo $_SESSION['id']?>">Профиль</a>
    					<a class="dropdown-item" href="/settings.php">Настройки</a>
    					<?php if($user->rights != 'user'):?>
    						<a class="dropdown-item" href="/admin.php">Админ панель</a>
    					<?php endif;?>
    					<div class="dropdown-divider"></div>
    					<a class="dropdown-item" href="/scripts/logout.php">Выход</a>
    				</div>
    				<a href="/backet.php"><img style="margin-left:10px;width: 38px; height: 38px; border-radius: 100px;" src="<?php echo "../media/src/basket.png"; ?>"></a>
				</div>
			<?php endif;?>
		
	</div>
</header>
	<?php 
		if($user):
			if($user->email_verified == false):?>
		<div class="p-3" style="width: 100%; height: 100%; background: #960018; top:70px;">
			<p style="margin-left: 45%; color: white;" class="mb-0">Потдвердите почту</p>
		</div>
		<?php endif; 
	endif;?>
