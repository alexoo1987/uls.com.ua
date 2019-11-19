<div class="container">	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th>Категория</th>
				<th style="width: 400px;">Категории в текдоке</th>
				<th></th>
			</tr>
		</thead>
		<tbody>	
		<?php foreach($categories as $ti): ?>
				<tr>
					<td><?=$ti->id?></td>
					<td><?=$ti->name?></td>
					<td><?=$ti->tecdoc_ids?></td>
					<td>
					</td>
				</tr>
					<?php foreach($ti->get_children() as $ti2): ?>
						<tr>
							<td><?=$ti2->id?></td>
							<td>|___<?=$ti2->name?></td>
							<td><?=$ti2->tecdoc_ids?></td>
							<td>
							</td>
						</tr>
						<?php foreach($ti2->get_children() as $ti3): ?>
							<tr>
								<td><?=$ti3->id?></td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|___<?=$ti3->name?></td>
								<td><?php
									$tecdoc_cats = Model::factory('NewTecdoc')->get_tecdoc_category_name_by_category($ti3->id);
									if($tecdoc_cats)
									{
										foreach ($tecdoc_cats as $tecdoc_cat)
										{
											echo $tecdoc_cat['name'].", ";
										}
									}
								?></td>
								<td>
									<a class="btn btn-mini" href="<?=URL::site('admin/categories/newedit/'.$ti3->id);?>"><i class="icon-edit"></i> Редактировать</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endforeach; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>