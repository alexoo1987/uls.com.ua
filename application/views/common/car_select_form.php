<?php

	$new_tecdoc = Model::factory('NewTecdoc');

	$manufacturer_slug = Request::current()->param('manufacturer', false);
	$model_slug = Request::current()->param('model', false);
	$modification_slug = Request::current()->param('modification', false);
	$category_slug = Request::current()->param('category', false);;
	$years = false;
    $new_manufacturer = false;
    $new_model = false;

	if($manufacturer_slug AND $model_slug)
	{
		$category = ORM::factory('Category')->where('slug', '=', $model_slug)->find();
		if(!empty($category->id)) {
			$category_slug = $model_slug;
			$model_slug = "";
		}
	}

	if($manufacturer_slug AND $model_slug)
	{
		$years = array();
		$new_tecdoc_years = $new_tecdoc->get_years_model($manufacturer_slug,$model_slug);

		foreach ($new_tecdoc_years as $new_tecdoc_year) {
			$start_year = substr($new_tecdoc_year['start_date'], 0, 4);

			if(!empty($new_tecdoc_year['end_date'])) {
				$end_year = substr($new_tecdoc_year['end_date'], 0, 4);
			} else {
				$end_year = date('Y');
			}

			for($year = $start_year; $year <= $end_year; $year++) {
				if(!in_array($year, $years)) $years[] = $year;
			}
		}
		sort($years);
	}

	if($manufacturer_slug)
    {
        $category = ORM::factory('Category')->where('slug', '=', $manufacturer_slug)->find();
        if(!empty($category->id)) {
            $category_slug = $category->slug;
			$new_manufacturer = false;
        } else {
            $new_manufacturer = $new_tecdoc->get_manuf_info_by_url($manufacturer_slug);
        }
    }

    if($model_slug)
    {
		$category = ORM::factory('Category')->where('slug', '=', $manufacturer_slug)->find();
		if(!empty($category->id)) {
			$category_slug = $category->slug;
			$new_model = false;
		} else {
			$new_model = $new_tecdoc->get_one_info_for_url_model($model_slug, $manufacturer_slug);
		}
    }

	if($modification_slug) {
		$category = ORM::factory('Category')->where('slug', '=', $modification_slug)->find();
		if(!empty($category->id)) {
			$category_slug = $category->slug;
		}
	}
?>
<div class="car-select" id="fix-car">
	<div class="clearfix"></div>
	<div class="row" data-gutter="15">
		<br>
		<div class="col-md-12">
			<div class="product">
				<p class="widget-title-lg none-fw pro-h title_select">Для начала выберите автомобиль:</p>
				<div class="car-choose-form" data-url="<?= URL::site("katalog/car_choose"); ?>">
					<div class="col-md-12">
						Выбор автомобиля позволяет отобразить только те запчасти, которые подходят к вашему автомобилю.
					</div>
					<div class="col-md-8" style="margin-top: 5px;"><!-- style="width: 22%; padding-right:0px;" -->

						<div class="car-choose btn-group open" data-car-creteria="year" data-info="">
							<button class="btn  btn-default dropdown-toggle" data-toggle="dropdown" >
								<span class="title">Год выпуска</span>
								<span class="name"></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<?php echo View::factory('car_select_form/years')->set('years', $years)->render(); ?>
							</ul>
						</div>
						<div class="car-choose btn-group <?= $new_manufacturer ? "fixed_value" : "inactive" ?>"
							 data-car-creteria="manuf" data-info="<?= $new_manufacturer ? $new_manufacturer['tecdoc_id'] : "" ?>">
							<button class="btn  btn-default dropdown-toggle disabled" data-toggle="dropdown"  >
								<span class="title" <?= $new_manufacturer ? 'style="display: none;"' : "" ?>>Выберите производителя</span>
								<span class="name"><?= $new_manufacturer ? strtoupper($new_manufacturer['short_name']) : "" ?></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-html" role="menu">
							</ul>
						</div>
						<div class="car-choose btn-group <?= $model_slug ? "fixed_value" : "hidden" ?>" data-car-creteria="model"
							 data-info="<?= $model_slug ? $model_slug : "" ?>" data-slug="<?= $model_slug ? $model_slug : "" ?>" >
	<!--						$model ? $model['id'] : -->
							<button class="btn  btn-default dropdown-toggle disabled" data-toggle="dropdown" >
								<span class="title" <?= $new_model ? 'style="display: none;"' : "" ?>>Выберите модель</span>
								<span class="name" data-info="<?= $new_model ? strtoupper($new_model['short_name']) : "" ?>"><?= $new_model ? strtoupper($new_model['short_name']) : "" ?></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-html" role="menu">
							</ul>
						</div>
	<!--				</div>-->
	<!--				<div class="col-md-12" style="width: 22%; padding-right:0px;">-->
						<div class="car-choose btn-group hidden" data-car-creteria="body_type" data-info="">
							<button class="btn  btn-default dropdown-toggle" data-toggle="dropdown" >
								<span class="title">Выберите тип кузова</span>
								<span class="name"></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-html" role="menu">
							</ul>
						</div>
						<div class="car-choose btn-group hidden" data-car-creteria="liters_fuel" data-info="" data-liters="">
							<button class="btn  btn-default dropdown-toggle" data-toggle="dropdown" >
								<span class="title">Выберите тип топлива и объем</span>
								<span class="name"></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-html" role="menu">
							</ul>
						</div>
						<div class="car-choose btn-group hidden" data-car-creteria="car_mod" data-info=""
							 data-url="<?= URL::site("katalog/set_car_mod"); ?>">
							<button class="btn  btn-default dropdown-toggle" data-toggle="dropdown" >
								<span class="title">Выберите модификацию</span>
								<span class="name"></span>
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-html" role="menu">
							</ul>
						</div>
					</div>

				</div>
				<input type="hidden" name="category_slug" value="<?= $category_slug ? $category_slug : "" ?>">
	
	<!--			<div class="col-md-3">-->
	<!--				<img src="--><?//= URL::base(); ?><!--media/img/car_icon.png">-->
	<!--			</div>-->
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<div id="fix-car-height" class="none" style="height: 250px">
</div>