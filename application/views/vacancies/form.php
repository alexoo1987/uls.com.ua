<div class="container">
	<div class="comments_container">
		<h1>Вакансии</h1>
		<hr>
		<? if(count($comments) > 0):?>
			<? if(count($comments) == 1): ?>
				<? foreach($comments as $comment): ?>
					<div class="row comment">
						<div class="span2">
							<span class="block widget-title-lg"><?=$comment->title?></span><br>
							<span class="telephone_header">Зароботная плата: <?=$comment->salary?></span><br>
							<span class="telephone_header">Занятость: <?=$comment->employment?></span><br>
							<span class="telephone_header">Опыт работы: <?=$comment->experiance?></span><br><br>
						</div>
						<div class="span6 comment-text">
							<span class="telephone_header">Описание вакансии:</span><br>
							<p><?=$comment->description?></p><br>
						</div>
						<div class="span6 comment-text">
							<span class="telephone_header">Результаты, которые Ми от Вас ожидаем:</span><br>
							<p><?=$comment->vaiting_results?></p><br>
						</div>
						<div class="span6 comment-text">
							<span class="telephone_header">Требования:</span><br>
							<p><?=$comment->requirements?></p><br>
						</div>
						<div class="span6 comment-text">
							<span class="telephone_header">Условия работы:</span><br>
							<p><?=$comment->working_conditions?></p><br>
						</div>
						<div class="span6 comment-text">
							<span class="telephone_header">Испытательный срок:</span><br>
							<p><?=$comment->probation?></p><br>
						</div>
						<div class="span6 comment-text">
							<p><?=$comment->meta_description?></p><br>
						</div>
					</div>
					<hr>
				<? endforeach; ?>
				<?=$pagination?>
			<? elseif(count($comments) > 1): ?>
				<? foreach($comments as $comment): ?>
					<span class="telephone_header"><a href="<?= URL::site('vacancies/index'); ?>?id=<?= $comment->id ?>"><?=$comment->title?></a></span><br><br><hr>
				<? endforeach; ?>
				<span class="telephone_header"><a href="https://www.work.ua/jobs/by-company/165822/#open-jobs" target="_blank">Наши вакансии</a> на сайте <a rel="nofollow, noindex" href="https://www.work.ua/" target="_blank">Work.ua</a></span><br><br><hr>
			<? endif; ?>
		<? else: ?>
			<p>Вакансии пока отсутствуют</p>
			<hr>
		<? endif; ?>
	</div>


	<!--	<a name="fb_share">Поделиться</a>-->
	<!--	<script src="https://www.facebook.com/connect.php/js/FB.Share" type="text/javascript"></script>-->

	<!--	<a name="fb_share" type="icon"-->
	<!--	   share_url="http://eparts.my/vacancies/index">Поделиться</a>-->
	<!--	<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript">-->
	<!--	</script>-->
	<!---->
	<!--	<a name="fb_share" type="icon_link"-->
	<!--	   share_url="http://eparts.my/vacancies/index">Поделиться</a>-->
	<!--	<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share"-->
	<!--			type="text/javascript">-->
	<!--	</script>-->
	<script type="text/javascript">(function() {
			if (window.pluso)if (typeof window.pluso.start == "function") return;
			if (window.ifpluso==undefined) { window.ifpluso = 1;
				var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
				s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
				s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
				var h=d[g]('body')[0];
				h.appendChild(s);
			}})();</script>
	<div class="pluso" data-background="transparent" data-options="medium,square,line,horizontal,nocounter,theme=04" data-services="vkontakte,odnoklassniki,facebook,twitter,google,moimir"></div>
</div>
