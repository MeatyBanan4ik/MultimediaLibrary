<?php session_start();
if(isset($_SESSION['id'])){
  header('Location: /');
}?>
<!DOCTYPE html>
<html style="height: 100%; width: 100%;">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <script src="css/jquery-3.2.1.slim.min.js"></script>
  <script src="css/popper.min.js"></script>
  <script src="css/bootstrap.min.js"></script>
  <title>Авторизация</title>
</head>
<body style="height: 100%; width: 100%;">
  <?php  require "blocks/header.php";?>   
  <div style="display: flex; align-items: center; height: 80%;">
    <div class="p-3 border border-secondary box-shadow" style="max-width: 400px; margin: auto; width: 100%;">
        <form action="scripts/auth.php" method="POST">
              <input type="text" name="login" placeholder="Введите логин/e-mail" maxlength="50" class="form-control" required value=<?php echo @$_SESSION['data_a']; ?>>
              <br/>
              <input type="password" name="password" placeholder="Введите пароль" maxlength="100" class="form-control" required>
              <div class="row justify-content-between">
                <div class="col mt-3">
                  <button type="submit" name="sign_in" class="btn btn-outline-success" autofocus>Войти</button>
                </div>
                <div class="col col-md-auto  mt-3">
                  <a class="btn btn btn-outline-info" href="signup.php">Зарегистрироваться</a>
                </div>
              </div>
          </form>
      </div>   
    </div>
    <?php if(!empty($_SESSION['errors_am'])):?>
        <div style = "width: 100%;  position:absolute; bottom: 0px;" class="row justify-content-end">
          <div class="alert alert-danger alert-dismissible show  p-3" id="close">
            <h6 class="alert-heading">Ошибка</h6>
            <button type="button" class="close p-2" id="close_a">
              <span>&times;</span>
            </button>
            <hr>
            <?php echo $_SESSION['errors_am'][0] . (($_SESSION['errors_am'][1] == "1") ? "" : "<br/><a href=\"/remember.php?login=" . @$_SESSION['data_a'] ."\">Забыли пароль?</a>");?>
        </div>
      </div>
      <script>
       document.getElementById('close_a').onclick = function() {
          document.getElementById('close').hidden = true;
        }
      </script>
    <?php unset($_SESSION['errors_am']); unset($_SESSION['data_a']); endif;?> 
    
</body>
</html>