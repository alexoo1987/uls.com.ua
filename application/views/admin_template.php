<!DOCTYPE html>
<html lang="<?php echo substr(I18n::$lang, 0, 2); ?>">
<meta charset="utf-8">
<title><?= $title; ?></title>
<meta name="description" content="<?= $description; ?>">
<!--<meta name="keywords" content="--><?//= $keywords; ?><!--">-->
<meta name="author" content="<?= $author; ?>">
<head>
<? foreach ($styles as $style) : ?>
	<link rel="stylesheet" href="<?= URL::base(); ?>media/css/<?= $style; ?>.css" />
<? endforeach; ?>


<title><?= $title; ?></title>
	<!-- Preloader-->
	<style>
		#loading{
			background-color: white;
			height: 100%;
			width: 100%;
			position: fixed;
			z-index: 1000;
			margin-top: 0px;
			top: 0px;
		}
		#loading-center{
			width: 100%;
			height: 100%;
			position: relative;
		}
		#loading-center-absolute {
			position: absolute;
			left: 50%;
			top: 40%;
			height: 150px;
			width: 150px;
			margin-top: -75px;
			margin-left: -75px;
			-ms-transform: rotate(45deg);
			-webkit-transform: rotate(45deg);
			transform: rotate(45deg);

		}
		#loading-image{
			position: absolute;
			top: 60%;
			left: calc(50% - 60px);
		}
		#loading-image img{
			max-width: 131px;
			width: 100%;
		}
		.object{
			width: 20px;
			height: 20px;
			background-color: #009260;
			position: absolute;
			left: 65px;
			top: 65px;
			-moz-border-radius: 50% 50% 50% 50%;
			-webkit-border-radius: 50% 50% 50% 50%;
			border-radius: 50% 50% 50% 50%;
		}
		.object:nth-child(2n+0) {
			margin-right: 0px;

		}
		#object_one {$display_window
			-webkit-animation: object_one 2s infinite;
			animation: object_one 2s infinite;
			-webkit-animation-delay: 0.2s;
			animation-delay: 0.2s;
		}
		#object_two {
			-webkit-animation: object_two 2s infinite;
			animation: object_two 2s infinite;
			-webkit-animation-delay: 0.3s;
			animation-delay: 0.3s;
		}
		#object_three {
			-webkit-animation: object_three 2s infinite;
			animation: object_three 2s infinite;
			-webkit-animation-delay: 0.4s;
			animation-delay: 0.4s;
		}
		#object_four {
			-webkit-animation: object_four 2s infinite;
			animation: object_four 2s infinite;
			-webkit-animation-delay: 0.5s;
			animation-delay: 0.5s;
		}
		#object_five {
			-webkit-animation: object_five 2s infinite;
			animation: object_five 2s infinite;
			-webkit-animation-delay: 0.6s;
			animation-delay: 0.6s;
		}
		#object_six {
			-webkit-animation: object_six 2s infinite;
			animation: object_six 2s infinite;
			-webkit-animation-delay: 0.7s;
			animation-delay: 0.7s;
		}
		#object_seven {
			-webkit-animation: object_seven 2s infinite;
			animation: object_seven 2s infinite;
			-webkit-animation-delay: 0.8s;
			animation-delay: 0.8s;
		}
		#object_eight {
			-webkit-animation: object_eight 2s infinite;
			animation: object_eight 2s infinite;
			-webkit-animation-delay: 0.9s;
			animation-delay: 0.9s;
		}

		#object_big{

			position: absolute;
			width: 50px;
			height: 50px;
			left: 50px;
			top: 50px;
			-webkit-animation: object_big 2s infinite;
			animation: object_big 2s infinite;
			-webkit-animation-delay: 0.5s;
			animation-delay: 0.5s;
		}
		@-webkit-keyframes object_big {
			50% { -webkit-transform: scale(0.5); }
		}
		@keyframes object_big {
			50% {
				transform: scale(0.5);
				-webkit-transform: scale(0.5);
			}
		}
		@-webkit-keyframes object_one {
			50% { -webkit-transform: translate(-65px,-65px)  ; }

		}
		@keyframes object_one {
			50% {
				transform: translate(-65px,-65px) ;
				-webkit-transform: translate(-65px,-65px) ;
			}
		}
		@-webkit-keyframes object_two {
			50% { -webkit-transform: translate(0,-65px) ; }
		}
		@keyframes object_two {
			50% {
				transform: translate(0,-65px) ;
				-webkit-transform: translate(0,-65px) ;
			}
		}
		@-webkit-keyframes object_three {
			50% { -webkit-transform: translate(65px,-65px) ; }
		}
		@keyframes object_three {
			50% {
				transform: translate(65px,-65px) ;
				-webkit-transform: translate(65px,-65px) ;
			}
		}
		@-webkit-keyframes object_four {
			50% { -webkit-transform: translate(65px,0) ; }
		}
		@keyframes object_four {
			50% {
				transform: translate(65px,0) ;
				-webkit-transform: translate(65px,0) ;
			}
		}
		@-webkit-keyframes object_five {
			50% { -webkit-transform: translate(65px,65px) ; }
		}
		@keyframes object_five {
			50% {
				transform: translate(65px,65px) ;
				-webkit-transform: translate(65px,65px) ;
			}
		}
		@-webkit-keyframes object_six {
			50% { -webkit-transform: translate(0,65px) ; }
		}
		@keyframes object_six {
			50% {
				transform:  translate(0,65px) ;
				-webkit-transform:  translate(0,65px) ;
			}
		}
		@-webkit-keyframes object_seven {

			50% { -webkit-transform: translate(-65px,65px) ; }

		}
		@keyframes object_seven {
			50% {
				transform: translate(-65px,65px) ;
				-webkit-transform: translate(-65px,65px) ;
			}
		}
		@-webkit-keyframes object_eight {
			50% { -webkit-transform: translate(-65px,0) ; }
		}
		@keyframes object_eight {
			50% {
				transform: translate(-65px,0) ;
				-webkit-transform: translate(-65px,0) ;
			}
		}
	</style>
</head>

<body class="preview" id="top">
<!-- Preloader -->
<!--<div id="loading">-->
<!--	<div id="loading-center">-->
<!--		<div id="loading-center-absolute">-->
<!--			<div class="object" id="object_one"></div>-->
<!--			<div class="object" id="object_two"></div>-->
<!--			<div class="object" id="object_three"></div>-->
<!--			<div class="object" id="object_four"></div>-->
<!--			<div class="object" id="object_five"></div>-->
<!--			<div class="object" id="object_six"></div>-->
<!--			<div class="object" id="object_seven"></div>-->
<!--			<div class="object" id="object_eight"></div>-->
<!--			<div class="object" id="object_big"></div>-->
<!--		</div>-->
<!--		<div id="loading-image">-->
<!--			<img src="http://eparts.kiev.ua/media/img/dist/logo-w.png" alt=""/>-->
<!--		</div>-->
<!--	</div>-->
<!--</div>-->
<!--end preloader -->
	<div id="wrap">
		<?php if (Auth::instance()->logged_in()) {
			echo View::factory('common/admin_menu')->render();

			//write last user activity
			$user_id = Auth::instance()->get_user()->id;
			DB::update('users')->set(array('last_activity' => date('Y-m-d H:i:s'), 'last_ip' => Request::$client_ip))->where('id', '=', $user_id)->execute();
		} ?>
		<div class="container<?php if(Auth::instance()->logged_in()) echo " admin-container"; ?>">
			<header class="container"><?= View::factory('common/admin_header')->render(); ?></header>
			<div class="container">

				<h1><?= $title; ?></h1>
				<?= $content; ?>

			</div>
		</div>
		<div id="push"></div>
	</div>
	<?php if(Auth::instance()->logged_in()): ?>
		<div id="chat_container" class="partialy_hidden" data-update-url="<?= URL::site("admin/ajax/get_msgs"); ?>">
			<div class="chat_msgs">
				<?php $user = Auth::instance()->get_user(); ?>
				<?php $msgs = ORM::factory('Message')->order_by('timestamp', 'desc')->find_all()->as_array(); ?>
				<?php foreach($msgs as $msg): ?>
					<div class="msg <?=$msg->user_id == $user->id ? "left" : "right" ?>">
						<b class="name"><?=$msg->user->surname?></b><br><i class="date"><?php $d = new DateTime($msg->timestamp); ?><?=$d->format('d.m H:i:s')?></i><br>
						<?=$msg->message?>
					</div>
				<?php endforeach; ?>
			</div>
			<div id="chat_controls">
				<input type="hidden" id="msg_last_id" value="<?=$msgs[0]->id?>" />
				<textarea name="message"></textarea>
				<button data-url="<?= URL::site("admin/ajax/send_msg"); ?>">Отправить</button>
			</div>
			<div>
				<a href="#" id="chat_btn" class="btn btn-mini btn-succes">Развернуть</a>
				<a href="#" id="chat_btn_close" class="btn btn-mini btn-danger">Свернуть</a>
			</div>
		</div>
		<?php

			$display_window_warning = false;
			if((ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Програмист') OR ORM::factory('Permission')->checkRole('packer') OR ORM::factory('Permission')->checkRole('Руководитель отделения склада')) AND !Cookie::get('warning_order'))
			{
				$display_window_warning = true;
			}

			$display_np_window_warning = false;
			if((ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Програмист') OR ORM::factory('Permission')->checkRole('Руководитель отделения продаж') OR ORM::factory('Permission')->checkRole('manager') OR ORM::factory('Permission')->checkRole('Старший Менеджер')) AND !Cookie::get('warning_np'))
			{
				$display_np_window_warning = true;
			}

			$display_window = true;
			if(Cookie::get('birthday_congratulations_close_event')){
				$close_event = true;
			}else{
				$close_event = false;
			}

			$session = Session::instance();
			$current_user = $session->get('auth_user');
			$current_user_id = $current_user->id;
			$user_data = ORM::factory('BirthdayDisplay')->where('user_id', '=', $current_user_id)->find();
			$birthday_congratulations = ORM::factory('BirthdaySettings')->where('name','=','employee')->find()->value;

			if($user_data->loaded()){
				if($user_data->date == date('Y-m-d')  ){
					$display_window = false;
				}
			}

			$current_date = date("m-d");
			$current_year = date("Y");
			$users = ORM::factory('User')->where('status', '=', 1)->find_all()->as_array();
			$current_users_birth_date = array();
			
			foreach ($users as $user) {
				if ($user->birth_date) {
					$birth_date = DateTime::createFromFormat("Y-m-d", $user->birth_date)->format('m-d');
					$user_age = $current_year  - DateTime::createFromFormat("Y-m-d", $user->birth_date)->format('Y');

					if ($birth_date === $current_date ) {						
						$current_users_birth_date[] = array('id'=>$user->id, 'full_name'=>$user->name.' '.$user->surname , 'age'=>$user_age);
					}
				}
			}
		?>
		<?php if($current_users_birth_date and  $display_window ):?>
			<?php foreach($current_users_birth_date as $user) : ?>
				<?php if($current_user_id == $user['id'] && !$close_event):?>
					<div id="window_congratulations">
						<div class="header-content">
							<?= $user['full_name']; ?>
							<span class="close" style="margin-right: 5px;">×</span>
						</div>
						<div class="main-content">
							<?=$birthday_congratulations;?>
						</div>
					</div>
				<?php endif;?>
			<?php endforeach;?>
		<?php endif;?>
		<?php
			foreach($current_users_birth_date as $k=> $u){
				if($u['id'] == $current_user_id){
					unset($current_users_birth_date[$k]);
				}
			}
		?>
		<?php if($current_users_birth_date and  $display_window ):?>
			<div id='birthday_window'>
				<table class="table table-striped table-bordered">
				<tr>
					<th colspan="2">Сегодня день рождение у :
						<span class="close">×</span>
					</th>
				</tr>

				<?php foreach($current_users_birth_date as $user) : ?>
					<tr>												
						<td><?=$user['full_name']?></td>							
						<td><?=$user['age']?></td>							
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		<?php endif;?>

		<?php if($display_window_warning AND isset($order_items_warning) AND $_SERVER['REQUEST_URI'] != '/admin/orders/items_warning'): ?>
			<div id='warning_order'>
				<table class="table table-striped table-bordered">
					<tr>
						<th colspan="2">
							<span class="close">×</span>
						</th>
					</tr>
				</table>
				<div class="alert alert-danger" role="alert">ВНИМАНИЕ!!! Есть позиции, которые нужно срочно заказать у поставщика</div>
				<a href="/admin/orders/items_warning">Посмотреть позиции</a>
			</div>
		<?php endif; ?>

		<?php if($display_np_window_warning): ?>
			<div id='warning_np_order'>
				<table class="table table-striped table-bordered">
					<tr>
						<th colspan="2">
							<span class="close">×</span>
						</th>
					</tr>
				</table>
				<div class="alert alert-danger" role="alert">ВАЖНО!!! Озвучивайте условия работы наложеного платежа по новой почте 2% +20грн от суммы заказа.</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($newPaidOrders)): ?>
			<div id="new_paid_orders">
				<table class="table table-striped table-bordered">
					<tr>
						<th>Новые оплаты заказов онлайн</th>
						<th colspan="2">
							<span class="close">×</span>
						</th>
					</tr>
				</table>
				<table class="table table-striped table-bordered">
					<tr>
						<th>Заказ</th>
						<th>Сумма</th>
					</tr>
					<?php foreach ($newPaidOrders as $item): ?>
						<tr>
							<td><a href="<?= Helper_Url::createUrl('/admin/orders/items/' . $item->order_id) ?>" target="_blank"><?= $item->order_id ?></a></td>
							<td><?= $item->value ?> грн.</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		<?php endif; ?>

		<style>
			#window_congratulations, #warning_order, #warning_np_order, #new_paid_orders{
				background-color: #fff;
				border: 4px solid #ddd;
				border-radius: 15px;
				top: 50%;
				left: 50%;
				width: 600px;
				height: 400px;
				position: fixed;
				margin-top: -200px;
				margin-left: -300px;
			}

			#warning_order, #warning_np_order, #new_paid_orders{
				height: 250px;
			}

			#window_congratulations .header-content,
			#warning_order .header-content,
			#new_paid_orders .header-content,
			#warning_np_order .header-content {
				text-align: center;
				padding-top:10px;
				height: 30px;
				width: 100%;
				border-bottom: 3px solid #ddd;
			}

			#window_congratulations .main-content,
			#warning_order .main-content,
			#warning_np_order .main-content,
			#new_paid_orders .main-content {
				padding: 25px;
			}
			#warning_order a {display: block; width: 40%; margin: 40px auto; border: 1px solid red; color: white; background: red; text-transform: uppercase; text-align: center; padding: 20px; border-radius: 20px}
		</style>

	<?php endif; ?>
<? foreach ($scripts as $script) : ?>
	<script src="<?= URL::base(); ?>media/js/<?= $script; ?>.js" ></script>
<? endforeach; ?>

<footer>
	<div class="container">
	<?= View::factory('common/admin_footer')->render(); ?>
	</div>
</footer>

<script type='text/javascript' src='https://apimgmtstorelinmtekiynqw.blob.core.windows.net/content/MediaLibrary/Widget/Tracking/dist/track.min.js'></script>
</body>
</html>