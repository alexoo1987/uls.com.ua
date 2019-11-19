<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/vacancies/create/');?>"><i class="icon-edit"></i> Создать</a>
	<br>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="filter-false">ID</th>
				<th>Заголовок</th>
				<th>ЗП</th>
				<th>Занятость</th>
				<th>Опыт</th>

				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($comments as $comment) : ?>
		<tr>
			<td><?=$comment->id?></td>
			<td><?=$comment->title?></td>
			<td><?=$comment->salary?></td>
			<td><?=$comment->employment?></td>
			<td><?=$comment->experiance?></td>

			<td>
				<a class="btn btn-mini" href="<?=URL::site('admin/vacancies/edit/'.$comment->id);?>"><i class="icon-edit"></i> Редактировать</a><br>
				<a class="btn btn-mini" href="<?=URL::site('admin/vacancies/delete/'.$comment->id);?>"><i class="icon-edit"></i> Удалить</a>

			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<!--	--><?php
//	function working_days($count) {
//
//		$date              = date( 'd-m-Y' );
//
//		$day_week          = date( 'N', strtotime( $date ) );
//
//		$day_count         = $count + $day_week;
//
//		$week_count        = floor($day_count/5);
//
//		$holiday_count     = ( $day_count % 5 > 0 ) ? 0 : 2;
//
//		$week_day          = $week_count * 7 - $day_week + ( $day_count % 5 ) - $holiday_count;
//
//		$date_end          = date( "d-m-Y", strtotime( $date . " + $week_day day " ) );
//
//		$date_end_count    = date( 'N', strtotime( $date_end ) );
//
//		$holiday_shift     = $date_end_count > 5 ? 7 - $date_end_count + 1 : 0;
//
//		return date("d-m-Y", strtotime($date_end . " + $holiday_shift day "));
//	}
//
//	echo working_days(20);?>
	<?=$pagination?>
</div>