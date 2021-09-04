<?php
	require_once "rb.php";
	$commission = 0.1;
	$frozen = FALSE;
	R::addDatabase( 'non_auth', 'pgsql:host=localhost;dbname=prototype', 'non_authorized', 'non_authorized', $frozen);
	R::addDatabase( 'postgres', 'pgsql:host=localhost;dbname=prototype', 'postgres', 'vlad1212', $frozen);
	$email = 'meatybanana11@gmail.com';
 	

 	if(!R::testConnection()){
 	 	if(isset($_SESSION['id'])){
 				R::addDatabase( $_SESSION['id'], 'pgsql:host=localhost;dbname=prototype', $_SESSION['login'], $_SESSION['password'], $frozen);
 				R::selectDatabase($_SESSION['id']);
 				if(!R::testConnection()){
 					R::selectDatabase('non_auth');
 					R::exec('SET ROLE non_auth');
 					unset($_SESSION['id']);
					unset($_SESSION['login']);
					unset($_SESSION['password']);
 					}
 				else {
 			 		$user = R::findOne('clients', 'id = ?', [$_SESSION['id']]);
 					R::exec('SET ROLE "'. $user->rights .'";');
 			 	}
 			 	}
 			 	
 				
 				
 	 	else{
 	 		
 				R::selectDatabase('non_auth');
 				R::exec('SET ROLE non_auth');
 	 	}
 	 }
	
	if(isset($_SESSION['id'])) {
		$user = R::findOne('clients', 'id = ?', [$_SESSION['id']]);
		$user->last_t =	date("Y-m-d H:i:s");
		if($user->email_verified == false){
				$user->email_verified = 0;
			}
		R::store($user);
	}

	R::ext('xdispense', function($table_name) {
		return R::getRedBean()->dispense($table_name);
	});