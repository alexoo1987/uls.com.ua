<div class="container">
	<a class="btn btn-mini" href="<?=URL::site('admin/menu/add_item/'.$menu->id);?>"><i class="icon-plus"></i> Добавить</a><br /> 
	<a class="btn btn-mini" href="#" id="save_sorting"><i class="icon-ok-circle"></i> Сохранить сортировку</a><br /><br />
	<?php render_menu($items, $menu); ?>
	<script>
		var sortableMaxLevels = <?=$menu->max_levels?>;
		var sortingSaveLink = '<?=URL::site('admin/menu/save_sorting/'.$menu->id);?>';
	</script>
</div>

<?php
	function render_item($item, $menu, $current_level) {
?>
	<li id="list_<?=$item->id?>">
		<div><?=$item->name?> (#<?=$item->id?>) | 
		<a class="btn btn-mini" href="<?=URL::site('admin/menu/edit_item/'.$item->id);?>"><i class="icon-edit"></i> Редактировать</a>
		<?php if($current_level < $menu->max_levels) { ?><a class="btn btn-mini btn-info" href="<?=URL::site('admin/menu/add_item/'.$menu->id.'/'.$item->id);?>"><i class="icon-plus icon-white"></i> Добавить подменю</a><?php } ?>
		<a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/menu/delete_item/'.$item->id);?>"><i class="icon-remove icon-white"></i> Удалить</a></div>
		<?php render_menu($item->menuitems->find_all()->as_array(), $menu, false, ($current_level+1)); ?>
	</li>
<?php
	}
	
	function render_menu($items, $menu, $first = true, $current_level = 1) {
		if(!empty($items)) {
?>
	<ol<?=($first ? ' class="sortable"' : '')?>>
<?php
			foreach($items as $item) {
				render_item($item, $menu, $current_level);
			}
?>
	</ol>
<?php
		}
	}
?>