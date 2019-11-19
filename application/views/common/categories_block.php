<?php $tree_list = ORM::factory('Category')->where('level', '=', 0)->order_by('id')->find_all()->as_array();
$manufacturer_slug = Request::current()->param('manufacturer');
$model_slug = Request::current()->param('model');


$tecdoc = Model::factory('NewTecdoc');

if ($manufacturer_slug) {
    $manufacturer = $tecdoc->get_manuf_info_by_url($manufacturer_slug);
}

if ($model_slug) {
    $model = $tecdoc->get_one_info_for_url_model($model_slug, $manufacturer_slug);
} ?>
<?php if ($_SERVER['REQUEST_URI'] != '/'): ?>
	<?php if ($_SERVER['REQUEST_URI'] == '/katalog'): ?>
		<h1 CLASS="widget-title-lg"><?php echo $content_catalog->h1?$content_catalog->h1:'Каталог запчастей'; ?></h1>
	<?php else: ?>
		<span class="block widget-title-lg">Каталог запчастей<?=($manufacturer_slug ? ' ' . $manufacturer['short_name'] : '') . ($model_slug ? ' ' . $model['short_name'] : '')?></span>
	<?php endif; ?>
<?php endif; ?>
<div class="categories_home_block flex_div">
	<?php foreach ($tree_list as $ti) { ?>
		<?php
		$secondLevel = $ti->get_children();
		$flag2 = 0;
		$count2 = count($secondLevel);

		foreach ($secondLevel as $second)
		{
			$thirdLevel = $second->get_children();
			$flag3 = 0;
			$count3 = count($thirdLevel);
			foreach ($thirdLevel as $third)
			{
				if(isset($active_categories) && !in_array($third->id, $active_categories))
					$flag3++;
			}
			if($count3 == $flag3)
			{
				$flag2++;
				continue;
			}
		}

		if($count2 == $flag2)
			continue;
		?>
		<div class="in_flex_25 activate-dropdown-new">
			<?php $url = !empty($modification_url) ? trim($modification_url, '/') . '/' : (!empty($manufacturer_slug) ? trim($manufacturer_slug, '/') . '/' : ''); ?>
			<a class="banner-category home button-dr dropdown-new-button" href="/katalog/<?= $url . trim($ti->slug, '/')?>">
				<img class="banner-category-img" src="<?= URL::base(); ?>media/img/dist/icons/<?= $ti->image ?>" alt="<?= $ti->name ?>" title="<?= $ti->name ?>">
				<p class="banner-category-title-catalog text-lowercase pro-h"><?= $ti->name ?></p>
			</a>
			<?php $ti2 = $ti->get_children(); ?>
			<?php if(!empty($ti2)): ?>
				 <div class="dropdown-new-container">

                     <div class="dropdown-new">
						<ul>
							<?php foreach ($ti2 as $first_child): ?>
								<?php $ti3 = $first_child->get_children(); ?>
								<?php if(!empty($ti3)): ?>
									<?php foreach ($ti3 as $second_child): ?>
										<?php if(isset($active_categories)):
											if(in_array($second_child->id, $active_categories)): ?>
												<li>
													<a href="<?= Helper_Url::createUrl('katalog/' . (isset($modification_url)? $modification_url:'' ) . $second_child->slug)?>" <?=(Cookie::get('car_modification', NULL) ? 'rel="nofollow"' : '')?>><?= $second_child->name ?></a>
												</li>
											<?php endif; ?>
										<?php else :?>
											<li>
												<a href="/katalog/<?= (isset($manufacturer_slug) ? $manufacturer_slug . '/' . $second_child->slug : $second_child->slug) ?>"><?= $second_child->name; ?></a>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>

                 </div>
			<?php endif; ?>
		</div>
	<?php } ?>
</div>

