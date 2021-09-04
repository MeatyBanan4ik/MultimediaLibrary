<?php
	session_start();
	require_once "../add/db.php";
	if(isset($_POST) AND isset($_SESSION['id'])){
		$user = R::findOne('clients', 'id = ?', [$_SESSION['id']]);
		if($user) {
			$check = R::findOne('orders', 'id_client = ? AND state = \'backet\'', [$_SESSION['id']]);
			if($check) {
				$sum = R::getAll('SELECT round(SUM(multimedia.price)::numeric, 2) FROM multimedia JOIN multimedia_list ON multimedia.id =  multimedia_list.id_multimedia WHERE id_order = ?', [$check->id]);
				if($sum[0][round]) {
					if($sum[0][round] < $user->balance) {
						R::exec('UPDATE clients SET balance = balance - ? WHERE id = ?', [$sum[0][round], $_SESSION['id']]);
						$order = R::findOne('orders', 'id = ?', [$check->id]);
						$order->date = date("Y-m-d H:i:s");
						$order->state = 'paid';
						$authors = R::getAll('SELECT * FROM multimedia_list WHERE id_order = ?', [$check->id]);
						foreach ($authors as $a) {
							$multimedia = R::findOne('multimedia', 'id = ?', [$a[id_multimedia]]);
							R::exec('UPDATE clients SET balance = balance + ? WHERE id = ?', [($multimedia->price - ($multimedia->price*$commission)), $multimedia->id_client]);
							$subject = "=?utf-8?B?".base64_encode("Ваше мультимедиа купили")."?=";
							$headers = "From: $email\r\nContent-type: text/html;charset=utf-8\r\n";
							$message = "Ваше <a href='".$_SERVER['HTTP_HOST']."/template.php?id=".$multimedia->id."'>мультимедиа ".$multimedia->name."</a> только что купил <a href='".$_SERVER['HTTP_HOST']."/profile.php?id=".$_SESSION['id']."'>".R::findOne('clients', 'id = ?', [$_SESSION['id']])->login."</a>";
							mail(R::findOne('clients', 'id = ?', [$multimedia->id_client])->email, $subject, $message, $headers);
							}							
						R::store($order);
						header('Location: /settings.php?type=order_list&id='.$check->id);
						exit();
					}
					else {
						$_SESSION['backet'] = 'У вас недостаточно денег не балансе';
						header('Location: /backet.php');
						exit();
					}
				}

			} 
			else {
				header('Location: /');
				exit();
			}
			
		}
	}
	else {
		header('Location: /');
		exit();
	}
header('Location: /backet.php');