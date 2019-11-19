<div class="container">
    <div class="row">
        <div class="span6 offset3">
            <?= Form::open('', array('class' => 'form-horizontal nova_poshta')); ?>

            <div class="control-group">
                <?= Form::label('id', 'ID', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('id', $order->id, array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('client_name', 'Имя клиента', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('client_name', $order->client->name, array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('client_surname', 'Фамилия клиента', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('client_surname', $order->client->surname, array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('client_phone', 'Номер клиента', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('client_phone', preg_replace('/[^\p{L}\p{N}\s]/u', '', $order->client->phone), array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('warehouse_from', 'Отправление с', array('class' => 'control-label none')); ?>
                <div class="controls">
                    <?= Form::select('warehouse_from', $warehouse_from, 1, array('validate' => 'required', 'class' => 'none')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('region', 'Область', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::select('region', $area, 1,array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('city', 'Город', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::select('city', $city,1, array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('warehous', 'Отделение', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::select('warehous', $warehouse, 1,array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('places', 'Кол-во мест', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('places', NULL, array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('weight', 'Вес (кг), (min - 0,1)', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('weight', NULL, array('validate' => 'required')); ?>
                </div>
            </div>

<!--            <div class="control-group">-->
<!--                --><?//= Form::label('width', 'Ширина (в метрах)', array('class' => 'control-label')); ?>
<!--                <div class="controls">-->
<!--                    --><?//= Form::input('width', NULL, array('validate' => 'required')); ?>
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div class="control-group">-->
<!--                --><?//= Form::label('height', 'Высота (в метрах)', array('class' => 'control-label')); ?>
<!--                <div class="controls">-->
<!--                    --><?//= Form::input('height', NULL, array('validate' => 'required')); ?>
<!--                </div>-->
<!--            </div>-->
<!---->
<!--            <div class="control-group">-->
<!--                --><?//= Form::label('lenght', 'Глубина (в метрах)', array('class' => 'control-label')); ?>
<!--                <div class="controls">-->
<!--                    --><?//= Form::input('lenght', NULL, array('validate' => 'required')); ?>
<!--                </div>-->
<!--            </div>-->

<!--            <div class="control-group">-->
<!--                --><?//= Form::label('volume', 'Обьем (м3), (min - 0.0004)', array('class' => 'control-label')); ?>
<!--                <div class="controls">-->
<!--                    --><?//= Form::input('volume', NULL, array('validate' => 'required')); ?>
<!--                </div>-->
<!--            </div>-->

            <div class="control-group">
                <?= Form::label('cost', 'Оценочная стоимость', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('cost', $summ, array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('platezh', 'Наложенный платеж (грн)', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('platezh', $platezh, array('validate' => 'required')); ?>
                </div>
            </div>

            <!--            <div class="control-group">-->
            <!--                <div class="controls">-->
            <!--                    --><?//= Form::submit('create', 'Сформировать накладную', array('class' => 'btn btn-primary save_item')); ?>
            <!--                </div>-->
            <!--            </div>-->
            <?= Form::close(); ?>

            <a href="#" class="btn btn-primary" id="create_express_order" data-url="https://eparts.kiev.ua/admin/ajax/create_express_for_order"><i class="icon-white icon-print"></i> Сформировать экспресс-накладную</a>

            <a href="#" target="_blank" class="btn btn-primary" id="link_print" style="display: none">Ссылка на накладную</a>

            <!--            --><?php //var_dump($order->orderitems->ttn->find_all()->as_array()); ?>
        </div>
    </div>
</div>
