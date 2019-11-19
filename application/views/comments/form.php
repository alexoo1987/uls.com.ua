<div class="container">
	<div class="comments_container">
		<br>
        <? if ($message) : ?>
            <h3 class="alert alert-info">
                <?= $message; ?>
            </h3>
        <? endif; ?>
		<br>
		<h1><?= $header->title; ?></h1>
		<?= $header->content; ?>
		<hr>

		<? if(count($comments) > 0): ?>
			<? foreach($comments as $comment): ?>
<!--			<div class="row comment">-->
<!--				<div class="span2">-->
<!--					<span class="comment-name">Имя: --><?//=$comment->name?><!--</span><br>-->
<!--					<span class="comment-date">Дата: --><?php //$d = new DateTime($comment->date_time); ?><!----><?//=$d->format('d.m.Y H:i:s')?><!--</span><br>-->
<!--					<span class="comment-name">Номер заказа: --><?//=$comment->number_order?><!--</span><br>-->
<!--					<span class="comment-name">Что заказывали: --><?//=$comment->order_position?><!--</span><br>-->
<!--					<span class="comment-name">Оценка Компании Епартс в целом: --><?//=$comment->rating?><!--</span><br>-->
<!--					<span class="comment-name">Оценка работы менеджера: --><?//=$comment->manager_rating?><!--</span><br>-->
<!--					<span class="comment-name">Что понравилось: --><?//=$comment->like?><!--</span><br>-->
<!--					<span class="comment-name">Что не понравилось: --><?//=$comment->dis_like?><!--</span><br>-->
<!--					<span class="comment-name">Предложения чтобы Вы автозапчасти покупали именно у нас: --><?//=$comment->suggestions?><!--</span><br>-->
<!--					--><?php //if($comment->answer): ?>
<!--						<br>-->
<!--						<span class="comment-name" style="font-weight: bold;">Комментарий: --><?//=$comment->answer?><!--</span><br>-->
<!--					--><?php //endif;?>
<!--				</div>-->
<!--				<div class="span6 comment-text">-->
<!--				</div>-->
<!--			</div>-->

				<div class="row row-sm-gap" data-gutter="10">
					<div class="col-md-12">
						<div class="clearfix">
							<div class="box">
								<table class="table">
									<thead>
									<tr>
										<th><span class="block" style="text-align: left; font-size: 24px;"><?=$comment->name?></span></th>
										<th><span class="block" style="text-align: right; font-size: 24px;"><?php $d = new DateTime($comment->date_time); ?><?=$d->format('d.m.Y H:i:s')?></span></th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td style="width: 260px;">Номер заказа: </td>
										<td><?=$comment->number_order?></td>
									</tr>
									<tr>
										<td>Что заказывали: </td>
										<td><?=$comment->order_position?></td>
									</tr>
									<tr>
										<td>Оценка Компании Епартс в целом: </td>
										<td>

										<?php if($comment->rating - 10 == 0):?>
											<i class="fa fa-star" aria-hidden="true"></i>&nbsp
											<i class="fa fa-star" aria-hidden="true"></i>&nbsp
											<i class="fa fa-star" aria-hidden="true"></i>&nbsp
											<i class="fa fa-star" aria-hidden="true"></i>&nbsp
											<i class="fa fa-star" aria-hidden="true"></i>
											&nbsp&nbsp(Оценка: <?=$comment->rating?>/10)
										<?php else:?>
											<?php $k = 10-$comment->rating;?>
											<?php if($k%2==0):?>
												<?php for($i=0; $i<intval((10-$k)/2); $i++):?>
													<i class="fa fa-star" aria-hidden="true"></i>&nbsp
												<?php endfor;?>
												<?php for($i=0; $i<intval($k/2); $i++):?>
													<i class="fa fa-star-o" aria-hidden="true"></i>&nbsp
												<?php endfor;?>
												&nbsp&nbsp(Оценка: <?=$comment->rating?>/10)
											<?php else:?>
												<?php for($i=0; $i<intval((10-$k)/2); $i++):?>
													<i class="fa fa-star" aria-hidden="true"></i>&nbsp
												<?php endfor;?>
												<i class="fa fa-star-half-o" aria-hidden="true"></i>&nbsp
												<?php for($i=0; intval($i<$k/2); $i++):?>
													<i class="fa fa-star-o" aria-hidden="true"></i>&nbsp
												<?php endfor;?>
												&nbsp&nbsp(Оценка: <?=$comment->rating?>/10)
											<?php endif?>
										<?php endif?>

										</td>
									</tr>



									<tr>
										<td>Оценка работы менеджера: </td>
										<td>
											<?php if($comment->manager_rating - 10 == 0):?>
												<i class="fa fa-star" aria-hidden="true"></i>&nbsp
												<i class="fa fa-star" aria-hidden="true"></i>&nbsp
												<i class="fa fa-star" aria-hidden="true"></i>&nbsp
												<i class="fa fa-star" aria-hidden="true"></i>&nbsp
												<i class="fa fa-star" aria-hidden="true"></i>
												&nbsp&nbsp(Оценка: <?=$comment->manager_rating?>/10)
											<?php else:?>
												<?php $k = 10-$comment->manager_rating;?>
												<?php if($k%2==0):?>
													<?php for($i=0; $i<intval((10-$k)/2); $i++):?>
														<i class="fa fa-star" aria-hidden="true"></i>&nbsp
													<?php endfor;?>
													<?php for($i=0; $i<intval($k/2); $i++):?>
														<i class="fa fa-star-o" aria-hidden="true"></i>&nbsp
													<?php endfor;?>
													&nbsp&nbsp(Оценка: <?=$comment->manager_rating?>/10)
												<?php else:?>
													<?php for($i=0; $i<intval((10-$k)/2); $i++):?>
														<i class="fa fa-star" aria-hidden="true"></i>&nbsp
													<?php endfor;?>
													<i class="fa fa-star-half-o" aria-hidden="true"></i>&nbsp
													<?php for($i=0; intval($i<$k/2); $i++):?>
														<i class="fa fa-star-o" aria-hidden="true"></i>&nbsp
													<?php endfor;?>
													&nbsp&nbsp(Оценка: <?=$comment->manager_rating?>/10)
												<?php endif?>
											<?php endif?>
										</td>
									</tr>
									<tr>
										<td>Что понравилось: </td>
										<td><?=$comment->like?></td>
									</tr>
									<tr>
										<td>Что не понравилось: </td>
										<td><?=$comment->dis_like?></td>
									</tr>
									<tr>
										<td><span class="block">Комментарий: </span></td>
										<td><span class="block"><?=$comment->answer?></span></td>
									</tr>
									</tbody>
								</table>

							</div>

						</div>

					</div> </div>



			<hr>
			<? endforeach; ?>
			<?=$pagination?>
		<? else: ?>
			<p>Комментарии пока отсутствуют.</p>
			<hr>
		<? endif; ?>
	</div>
    <div class="row">



		<div class="col-md-12">
			<div class="clearfix">
				<span class="block widget-title-lg">Оставить отзыв: </span>
				<?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
					<div class="col-md-3">
						<div class="form-group">
							<?= Form::label('name', 'Имя*'/*, array('class' => 'control-label')*/); ?>
							<?= Form::input('name', HTML::chars(Arr::get($data, 'name')), array('class' => 'form-control', 'validate' => 'required')); ?>
						</div>
						<div class="form-group">
							<?= Form::label('number_order', 'Номер заказа*'/*, array('class' => 'control-label')*/); ?>
							<?= Form::input('number_order', HTML::chars(Arr::get($data, 'number_order')), array('class' => 'form-control', 'validate' => 'required')); ?>
						</div>
						<div class="form-group">
							<?= Form::label('order_position', 'Что заказывали*'/*, array('class' => 'control-label')*/); ?>
							<?= Form::input('order_position', HTML::chars(Arr::get($data, 'order_position')), array('class' => 'form-control', 'validate' => 'required')); ?>
						</div>
					</div>
					<div class="col-md-5">
						<div class="radio">
							<?= Form::label('rating', 'Как Вы оцениваете работу Компании Епартс в целом?*'/*, array('class' => 'control-label')*/); ?></br>
							<label><?= Form::radio('rating', 1, FALSE, array('class'=>'i-radio', 'validate' => 'required'));?>1</label>&nbsp
							<label><?= Form::radio('rating', 2, FALSE, array('class'=>'i-radio', 'validate' => 'required'));?>2</label>&nbsp
							<label><?= Form::radio('rating', 3, FALSE, array('class'=>'i-radio', 'validate' => 'required'));?>3</label>&nbsp
							<label><?= Form::radio('rating', 4, FALSE, array('class'=>'i-radio'));?>4</label>&nbsp
							<label><?= Form::radio('rating', 5, FALSE, array('class'=>'i-radio'));?>5</label>&nbsp
							<label><?= Form::radio('rating', 6, FALSE, array('class'=>'i-radio'));?>6</label>&nbsp
							<label><?= Form::radio('rating', 7, FALSE, array('class'=>'i-radio'));?>7</label>&nbsp
							<label><?= Form::radio('rating', 8, FALSE, array('class'=>'i-radio'));?>8</label>&nbsp
							<label><?= Form::radio('rating', 9, FALSE,  array('class'=>'i-radio'));?>9</label>&nbsp
							<label><?= Form::radio('rating', 10, TRUE, array('class'=>'i-radio'));?>10</label>&nbsp
						</div>
						<div class="radio">
							<?= Form::label('manager_rating', 'Работа менеджера*'/*, array('class' => 'control-label')*/); ?></br>

							<label><?= Form::radio('manager_rating', 1, FALSE,  array('class'=>'i-radio'));?>1</label>&nbsp
							<label><?= Form::radio('manager_rating', 2, FALSE,  array('class'=>'i-radio'));?>2</label>&nbsp
							<label><?= Form::radio('manager_rating', 3, FALSE,  array('class'=>'i-radio'));?>3</label>&nbsp
							<label><?= Form::radio('manager_rating', 4, FALSE,  array('class'=>'i-radio'));?>4</label>&nbsp
							<label><?= Form::radio('manager_rating', 5, FALSE,  array('class'=>'i-radio'));?>5</label>&nbsp
							<label><?= Form::radio('manager_rating', 6, FALSE,  array('class'=>'i-radio'));?>6</label>&nbsp
							<label><?= Form::radio('manager_rating', 7, FALSE,  array('class'=>'i-radio'));?>7</label>&nbsp
							<label><?= Form::radio('manager_rating', 8, FALSE,  array('class'=>'i-radio'));?>8</label>&nbsp
							<label><?= Form::radio('manager_rating', 9, FALSE,  array('class'=>'i-radio'));?>9</label>&nbsp
							<label><?= Form::radio('manager_rating', 10, TRUE, array('class'=>'i-radio'));?>10</label>&nbsp
						</div>

						<div class="checkbox">
							<?= Form::label('like', 'Что Вам понравилось*', array('class' => 'control-label')); ?></br>
							<label><?= Form::checkbox('like[]', 'Консультация менеджера ', FALSE,  array('class'=>'i-check')); ?> Консультация менеджера </label></br>
							<label><?= Form::checkbox('like[]', 'Цена ', FALSE,  array('class'=>'i-check')); ?> Цена </label></br>
							<label><?= Form::checkbox('like[]', 'Большой выбор ', FALSE,  array('class'=>'i-check')); ?> Большой выбор </label></br>
							<label><?= Form::checkbox('like[]', 'Быстрая доставка ', FALSE,  array('class'=>'i-check')); ?> Быстрая доставка </label></br>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<?= Form::label('dis_like', 'Не понравилось?*'/*, array('class' => 'control-label')*/); ?>
							<?= Form::textarea('dis_like', HTML::chars(Arr::get($data, 'dis_like')), array('class' => 'form-control', 'rows' => '2', 'validate' => 'required')); ?>
						</div>
						<div class="form-group">
							<?= Form::label('suggestions', 'Ваши предложения чтобы Вы автозапчасти <br>покупали именно у нас:*'/*, array('class' => 'control-label')*/); ?>
							<?= Form::textarea('suggestions', HTML::chars(Arr::get($data, 'suggestions')), array('class' => 'form-control', 'rows' => '3', 'style' => 'text-align: left', 'validate' => 'required')); ?>
						</div>
					</div>
					<div class="col-md-12" style="text-align: center;">
						<span class="block widget-title-lg"><?= Form::submit('submit', 'Добавить отзыв', array('class' => 'btn btn-primary')); ?></span>
					</div>
				<?= Form::close(); ?>
				<span class="block" style="text-align: center;">Благодарим за доверие!<br>
					С уважением команда  компании Епартс</span>
			</div>
		</div>
















<!---->
<!---->
<!---->
<!--		<div class="span6 offset1">-->
<!--			--><?// if ($message) : ?>
<!--				<h3 class="alert alert-info">-->
<!--					--><?//= $message; ?>
<!--				</h3>-->
<!--			--><?// endif; ?>
<!--			--><?//= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('name', 'Имя*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::input('name', HTML::chars(Arr::get($data, 'name')), array('validate' => 'required', )); ?>
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('number_order', 'Номер заказа*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::input('number_order', HTML::chars(Arr::get($data, 'number_order')), array('class' => 'bfh-phone', 'validate' => 'required')); ?>
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('order_position', 'Что заказывали*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::input('order_position', HTML::chars(Arr::get($data, 'order_position')), array('validate' => 'required')); ?>
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('rating', 'Как Вы оцениваете работу Компании Епартс в целом?*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::radio('rating', 1, array('style' => 'margin-right:5px', 'validate' => 'required'));?><!--1-->
<!--					--><?//= Form::radio('rating', 2, array('style' => 'margin-left:5px', 'validate' => 'required'));?><!--2-->
<!--					--><?//= Form::radio('rating', 3, array('style' => 'margin-left:5px', 'validate' => 'required'));?><!--3-->
<!--					--><?//= Form::radio('rating', 4, array('style' => 'margin-left:5px'));?><!--4-->
<!--					--><?//= Form::radio('rating', 5, array('style' => 'margin-left:5px'));?><!--5-->
<!--					--><?//= Form::radio('rating', 6, array('style' => 'margin-left:5px'));?><!--6-->
<!--					--><?//= Form::radio('rating', 7, array('style' => 'margin-left:5px'));?><!--7-->
<!--					--><?//= Form::radio('rating', 8, array('style' => 'margin-left:5px'));?><!--8-->
<!--					--><?//= Form::radio('rating', 9, array('style' => 'margin-left:5px'));?><!--9-->
<!--					--><?//= Form::radio('rating', 10, TRUE, array('style' => 'margin-left:5px'));?><!--10-->
<!---->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('manager_rating', 'Работа менеджера*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::radio('manager_rating', 1, array('style' => 'margin-right:5px', 'validate' => 'required'));?><!--1-->
<!--					--><?//= Form::radio('manager_rating', 2, array('style' => 'margin-left:5px', 'validate' => 'required'));?><!--2-->
<!--					--><?//= Form::radio('manager_rating', 3, array('style' => 'margin-left:5px', 'validate' => 'required'));?><!--3-->
<!--					--><?//= Form::radio('manager_rating', 4, array('style' => 'margin-left:5px'));?><!--4-->
<!--					--><?//= Form::radio('manager_rating', 5, array('style' => 'margin-left:5px'));?><!--5-->
<!--					--><?//= Form::radio('manager_rating', 6, array('style' => 'margin-left:5px'));?><!--6-->
<!--					--><?//= Form::radio('manager_rating', 7, array('style' => 'margin-left:5px'));?><!--7-->
<!--					--><?//= Form::radio('manager_rating', 8, array('style' => 'margin-left:5px'));?><!--8-->
<!--					--><?//= Form::radio('manager_rating', 9, array('style' => 'margin-left:5px'));?><!--9-->
<!--					--><?//= Form::radio('manager_rating', 10, TRUE, array('style' => 'margin-left:5px'));?><!--10-->
<!---->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('like', 'Что Вам понравилось*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::checkbox('like[]', 'Консультация менеджера '); ?><!-- Консультация менеджера <br>-->
<!--					--><?//= Form::checkbox('like[]', 'Цена '); ?><!-- Цена <br>-->
<!--					--><?//= Form::checkbox('like[]', 'Большой выбор '); ?><!-- Большой выбор <br>-->
<!--					--><?//= Form::checkbox('like[]', 'Быстрая доставка '); ?><!-- Быстрая доставка <br>-->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('dis_like', 'Не понравилось?*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('dis_like', HTML::chars(Arr::get($data, 'dis_like')), array('rows' => '3', 'validate' => 'required')); ?>
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				--><?//= Form::label('suggestions', 'Ваши предложения чтобы Вы автозапчасти <br>покупали именно у нас:*', array('class' => 'control-label')); ?>
<!--				<div class="controls">-->
<!--					--><?//= Form::textarea('suggestions', HTML::chars(Arr::get($data, 'suggestions')), array('rows' => '3', 'style' => 'text-align: left', 'validate' => 'required')); ?>
<!--				</div>-->
<!--			</div>-->
<!--			<div class="control-group">-->
<!--				<div class="controls">-->
<!--					--><?//= Form::submit('submit', 'Отправить', array('class' => 'btn btn-success')); ?>
<!--				</div>-->
<!--			</div>-->
<!--			--><?//= Form::close(); ?>
<!--		</div>-->
<!--		<br>-->
<!--		<br>-->
<!--		<h2>Благодарим за доверие!<br>-->
<!--			С уважением команда  компании Епартс</h2>-->
<!---->
<!--    </div>-->
</div>



