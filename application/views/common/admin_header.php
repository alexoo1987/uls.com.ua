<div class="container">
	<div class="row">
		<div class="span4">
		<span class="bold">Панель администратора</span>
		</div>
		<?php if(Auth::instance()->logged_in()) echo View::factory('common/admin_search_form')->render(); ?>
	</div>
</div>