<?php session_start();?>
<!DOCTYPE html>
<html style="height: 100%; width: 100%;">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <title>Восстановление пароля</title>
</head>
<body style="height: 100%; width: 100%;">
  <?php require "blocks/header.php";?>   
  <div style="display: flex; align-items: center; height: 80%;">
    <div class="p-3 border border-secondary box-shadow" style="max-width: 400px; margin: auto; width: 100%;">
        <form action="scripts/recovery.php <?php if(isset($_GET['l']) && isset($_GET['h'])) echo "?l=" . $_GET['l'] . "&h=" . $_GET['h']?>" method="POST">
        	  <?php 
        	  	$user = R::findOne('clients', "login = ?", [mb_convert_encoding(base64_decode($_GET['l']), "UTF-8")]);
        	  	$success = 0;
        	  	if ($user) {
        	  		//если юзер существует
        	  		$hash = md5($user->hash.$user->rec_t);
        	  		if ($_GET['h'] == $hash) {
        	  			//если хеш правильный
        	  			if(((strtotime(date("H:i:s d.m.Y")) - strtotime($user->rec_t))/60/60) < 24) {
        	  				//если Время действия ссылки еще не вышло
        	  				$success = 1;
        	  			}
        	  		}	  		
        	  	} if ($success == 1):
        	  	?>
        	  	  <p class="mb-1">Введите ваш пароль:</p>
        		  <input type="password" name="new_password" placeholder="Пароль" class="form-control <?php if (!empty($_SESSION['errors_rm'])) echo 'is-invalid';?>" required maxlength="100">
        	      <br/>
        		  <p class="mb-1">Введите ваш пароль еще раз:</p>
        		  <input type="password" name="new_password2" maxlength="30" placeholder="Повторите пароль" class="form-control <?php if (!empty($_SESSION['errors_rm'])) echo 'is-invalid';?>" required>
        		  <br/>
        		  <button type="submit" name="recovery" class="btn btn-outline-success" autofocus>Восстановить пароль</button>
        	  <?php else:?>	
	              <input type="text" name="login" placeholder="Введите логин/e-mail" maxlength="50" class="form-control" required value=<?php echo @$_GET['login']; ?>>
	              <br/>
	              <button type="submit" name="recovery" class="btn btn-outline-success" autofocus>Восстановить пароль</button>
          	  <?php endif;?>
          </form>
      </div>   
    </div>
    <?php if(!empty($_SESSION['errors_rm'])):?>
        <div style = "width: 100%;  position:absolute; bottom: 0px;" class="row justify-content-end">
          <div class="alert alert-danger alert-dismissible show  p-3" id="close">
            <h6 class="alert-heading">Ошибка</h6>
            <button type="button" class="close p-2" id="close_a">
              <span>&times;</span>
            </button>
            <hr>
            <?php if(isset($_GET['l']) && isset($_GET['h'])):?> 
            	<ul>
		  			<?php foreach($_SESSION['errors_rm'] as $e) echo"<li>$e</li>";?>
		  		</ul>
            <?php else: ?>
            	<?php echo $_SESSION['errors_rm'][0] . "<br/><a href=\"/signup.php\">Зарегистрироваться?</a>";?>
        	<?php endif;?>
        </div>
      </div>
      <script>
       document.getElementById('close_a').onclick = function() {
          document.getElementById('close').hidden = true;
        }
      </script>
    <?php unset($_SESSION['errors_rm']); endif;?> 
    
</body>
</html>