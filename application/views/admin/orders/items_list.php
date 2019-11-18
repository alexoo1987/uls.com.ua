<script>
    var print_url = '<?=URL::site('admin/orders/print_items');?>';
    var sticker_print_url = '<?=URL::site('admin/orders/print_sticker_items');?>';
    var edit_state_url = '<?=URL::site('admin/orders/edit_item_state');?>';
    var save_state_url = '<?=URL::site('admin/orders/save_item_state');?>';
    var salary_ok_url = '<?=URL::site('admin/orders/salary_ok');?>';
    var move_items_url = '<?=URL::site('admin/orders/move_items');?>';
</script>
<div class="container">
    <?php $show_summary = true;?>
    <?php $is_buyer = ORM::factory('Permission')->checkRole('Закупщик');?>
    <?php $page = isset($_GET['page']) ? $_GET['page'] : 1;?>


    <div class="flex_box">
        <div class="in_flex_50">
            <?php if(empty($order_id)) { ?>
                <?= Form::open('', array('class' => 'form-horizontal', 'method' => 'get')); ?>
                <div class="flex_box">
                    <div class="in_flex_30">Дата от</div>
                    <div class="in_flex_40">
                        <?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker')); ?>
                    </div>
                </div>
                <div class="flex_box">
                    <div class="in_flex_30">до</div>
                    <div class="in_flex_40">
                        <?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?>
                    </div>
                </div>
                <div class="flex_box">
                    <div class="in_flex_30">Артикул</div>
                    <div class="in_flex_40">
                        <?= Form::input('article', HTML::chars(Arr::get($filters, 'article'))); ?>
                    </div>
                </div>

                <? if(ORM::factory('Permission')->checkPermission('orders_show_supplier')) { ?>
                    <div class="flex_box">
                        <div class="in_flex_30">Поставщик</div>
                        <div class="in_flex_40">
                            <?= Form::select('supplier_id', $suppliers, Arr::get($filters, 'supplier_id')); ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="flex_box">
                    <div class="in_flex_30">Состояние</div>
                    <div class="in_flex_40">
                        <?= Form::select('state_id', $states, Arr::get($filters, 'state_id')); ?>
                    </div>
                </div>

                <? if(ORM::factory('Permission')->checkPermission('orders_show_manager') AND !ORM::factory('Permission')->checkRole('manager')) { ?>
                    <div class="flex_box">
                        <div class="in_flex_30">Менеджер</div>
                        <div class="in_flex_40">
                            <?= Form::select('manager_id', $managers, Arr::get($filters, 'manager_id')); ?>
                        </div>
                    </div>
                <?php } ?>


                <div class="flex_box">
                    <div class="in_flex_30">Имя клиента</div>
                    <div class="in_flex_40">
                        <?= Form::input('client_name', HTML::chars(Arr::get($filters, 'client_name'))); ?>
                    </div>
                </div>

                <div class="flex_box">
                    <div class="in_flex_30">Фамилия клиента</div>
                    <div class="in_flex_40">
                        <?= Form::input('client', HTML::chars(Arr::get($filters, 'client'))); ?>
                    </div>
                </div>

                <div class="flex_box">
                    <div class="in_flex_30">Телефон клиента </div>
                    <div class="in_flex_40">
                        <?= Form::input('phone', HTML::chars(Arr::get($filters, 'phone')), array('class' => 'bfh-phone', 'data-format' => '(ddd)ddd-dd-dd', 'data-number' => preg_replace('/[^0-9]/', '', HTML::chars(Arr::get($filters, 'phone'))), 'validate' => 'required|phone')); ?>
                    </div>
                </div>

                <div class="flex_box">
                    <div class="in_flex_30"># заказа </div>
                    <div class="in_flex_40">
                        <?= Form::input('order_id', HTML::chars(Arr::get($filters, 'order_id'))); ?>
                    </div>
                </div>

                <div class="flex_box">
                    <div class="in_flex_30">Архив</div>
                    <div class="in_flex_40">
                        <?= Form::select('archive', array('all' => 'Все', '0' => 'Нет', '1' => 'Да'), Arr::get($filters, 'archive')); ?>
                    </div>
                </div>

                <div class="flex_box">
                    <div class="in_flex_30">ЗП выплачено?</div>
                    <div class="in_flex_40">
                        <?= Form::select('salary', array('all' => 'Все', '0' => 'Нет', '1' => 'Да'), Arr::get($filters, 'salary')); ?>
                    </div>
                </div>

                <div class="flex_box">
                    <div class="in_flex_30"><?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary')); ?></div>
                    <?= Form::close(); ?>
                    <div class="in_flex_20"><a href="<?=URL::site('admin/orders/items');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Сброс</a><br /></div>
                </div>
            <?php } else { ?>
                <?=(!empty($order_details['order']->delivery_method) ? "<b>Метод доставки:</b> ".$order_details['order']->delivery_method->name."<br />" : "")?>
                <?php if($order_details['order']->delivery_method->id == 3): ?>
                    <b>Детали доставки: </b><?= $order_details['order']->getDeliveryNpDetails()?><br />
                <?php else: ?>
                    <?=(!empty($order_details['order']->delivery_address) ? "<b>Детали доставки:</b> ".$order_details['order']->delivery_address."<br />" : "")?>
                <?php endif; ?>
                <?=(!empty($order_details['order']->state) ? "<b>Тип заказа:</b> ".$order_details['order']->OrderState->name."<br />" : "")?>
                <?=(!empty($order_details['order']->manager_comment) ? "<b>Комментарий менеджера:</b> ".$order_details['order']->manager_comment."<br />" : "")?>
                <?=(!empty($order_details['order']->client_comment) ? "<b>Комментарий клиента:</b> ".$order_details['order']->client_comment."<br />" : "")?>

                <br />
                <b>Всего заказов на сумму:</b> <?=$order_details['all_sale']?> грн.<br />
                <b>Всего денег внесенно:</b> <?=$order_details['all_in']?> грн.<br />
                <b>Активных заказов на сумму:</b> <?=$order_details['active_sale']?> грн.<br />
                <b>Баланс без активных заказов:</b> <?=$order_details['balance']?> грн.<br />
                <b>Баланс учитывая активные заказы:</b> <?=$order_details['active_balance']?> грн.<br />
                <b>Долг:</b> <?=$order_details['debt']?> грн.<br />
                <br />
                <b>В заказе на сумму:</b> <?=$order_details['debt_in_order']?> грн.<br /><br />
                <a href="<?=URL::site('admin/clientpayment/list');?>?client_id=<?=$order_details['order']->client_id?>" class="btn btn-primary"><i class="icon-white icon-list-alt"></i> Подробный баланс по клиенту</a><br /><br />
                <?php if(!empty($order_details['order']->ttn)): ?>
                    <b>ТТН:</b> <?=$order_details['order']->ttn?><br />
                    <br />
                <?php endif; ?>

                <a class="btn" href="<?=URL::site('admin/cars/index/'.$order_details['order']->client_id);?>"><i class="icon-info-sign"></i> Автомобили клиента</a><br /><br />
                <a class="btn" href="<?=Helper_Url::createUrl('admin/orders/send_details/'.$order_details['order']->id);?>"><i class="icon-info-sign"></i> Отправить данные на почту и СМС</a><br /><br />
                <a class="btn" href="<?=Helper_Url::createUrl('admin/orders/send_liqpay_details/'.$order_details['order']->id);?>"><i class="icon-info-sign"></i> Отправить сообщение для оплаты</a><br /><br />
                <?= Form::open('', array('class' => 'form-horizontal')); ?>
                Частичная оплата:
                <?= Form::input('partial_payment', $order_details['order']->partial_payment) ?>
                <?= Form::submit('', 'Применить', array('class' => 'btn btn-primary')); ?>
                <?= Form::close() ?>
                <br />

                <!-- Send TTN to client -->
                <a href="#sendTTN" role="button" class="btn btn-info" data-toggle="modal"
                   data-phone="<?= $order_details['order']->client->phone ?>"
                   data-method="<?= $order_details['order']->delivery_method->name ?>"
                   data-order_id="<?= $order_details['order']->id ?>"
                   data-ttn="<?= (!empty($order_details['order']->ttn)) ? $order_details['order']->ttn : "" ?>"><i
                            class="icon-envelope"></i> Отправить
                    клиенту ТТН</a> <br/>

                <!-- TTN modal -->
                <div id="sendTTN" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3>Отправка ТТН клиенту по СМС</h3>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="input-block-level inputTTN" placeholder="Укажите номер ТТН" required>
                        <input type="hidden" class="phone">
                        <input type="hidden" class="method">
                        <input type="hidden" class="order_id">
                        <div class="alert alert-info">
                            Указанный номер товарно транспортной накладной будет отправлен клиенту с помощью СМС
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
                        <button class="btn btn-primary submitTTN" data-dismiss="modal" disabled="disabled">Отправить</button>
                    </div>
                </div>

                <? if(ORM::factory('Permission')->checkPermission('clientpayment_add')) { ?>
                    <b>Внести проплату от клиента:</b><br />
                    <?= Form::open('', array('class' => 'form-horizontal add_client_pay', 'id' => 'validate_form')); ?>
                    <? if ($message) : ?>
                        <h3 class="alert alert-info">
                            <?= $message; ?>
                        </h3>
                    <? endif; ?>
                    <div class="control-group">
                        <?= Form::label('value', 'Внесено', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?= Form::input('value', HTML::chars(Arr::get($data, 'value')), array('validate' => 'required|float')); ?>
                            <cpan class="add-on">грн.</cpan>
                        </div>
                    </div>


                    <?php if(ORM::factory('Permission')->checkPermission('card_managment')) : ?>
                        <div class="control-group">
                            <?= Form::label('type', 'Внесено', array('class' => 'control-label')); ?>
                            <div class="controls">
                                <?= Form::select('type', [1=>'Наличные',2=>'Карта'], Arr::get($data, 'type')); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="control-group">
                        <?= Form::label('comment_text', 'Коментарий', array('class' => 'control-label')); ?>
                        <div class="controls">
                            <?= Form::input('comment_text', HTML::chars(Arr::get($data, 'comment_text'))); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
                        </div>
                    </div>
                    <?= Form::close(); ?>
                <?php } ?>

                <br /><br />
                <a href="#" class="btn btn-primary print" data-type="default"><i class="icon-white icon-print"></i> Накладная</a>
                <a href="#" class="btn btn-primary print" data-type="cashless"><i class="icon-white icon-print"></i>ФОП Б/Н Счет</a>
                <a href="#" class="btn btn-primary print" data-type="sales_invoice"><i class="icon-white icon-print"></i>ФОП Расходная Накладная</a>
                <a href="#" class="btn btn-primary print" data-type="cashless_tov"><i class="icon-white icon-print"></i>ТОВ Б/Н Счет</a>
                <a href="#" class="btn btn-primary print" data-type="sales_invoice_tov"><i class="icon-white icon-print"></i>ТОВ Расходная Накладная</a>
                <!--		--><?php //if(!$order_details['order']->np_area AND !$order_details['order']->np_city AND !$order_details['order']->np_warehouse): ?>
                <a href="#" class="btn btn-primary" id="print_sticker"><i class="icon-white icon-tag"></i> Стикер</a><br />
                <!--		--><?php //endif; ?>
                <br><br>

                <div class="control-group">
                    <?= Form::label('cash_amount', 'Сумма квитанции:', array('class' => 'control-label')); ?>
                    <div class="controls">
                        <?= Form::input('cash_amount', HTML::chars($order_details['debt_in_order']), array('id' => 'cash_amount', 'validate' => 'required|float')); ?>
                        <cpan class="add-on">грн.</cpan>
                    </div>
                </div>
                <a href="#" class="btn btn-primary" id="print_bill" data-url="<?=URL::site('admin/orders/print_bill');?>"><i class="icon-white icon-print"></i> Квитанция</a>
                <br><br>

                <? if(ORM::factory('Permission')->checkPermission('move_items')) { ?>
                    <br /><?= Form::select('order_id', $orders_to_move, Arr::get($data, 'order_id'), array('id' => 'move_to_order')); ?>
                    <?= Form::hidden('current_order', $order_details['order']->id, array('id' => 'current_order')); ?>
                    <a href="#" class="btn btn-primary" id="move_items"><i class="icon-white icon-share-alt"></i> Переместить</a><br />
                <?php } ?>
            <?php } ?>
        </div>
        <div class="in_flex_50">

            <div class="flex_box">
                <div class="in_flex_50">
                    <div class="well">
                        <h4>Сроки доставки</h4>
                        <div>
                            <?php foreach ($filters['in_work'] AS $key => $one) { ?>
                                <p><a href="<?=URL::site('admin/orders/items');?>?ids=<?=implode(',', $one['ids'])?>">
                                        <span style="background: <?=$one['color']?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                        <?=($one['label'] . '('.count($one['ids']).')')?>
                                    </a></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="in_flex_50">
                    <div class="well">
                        <h4>Сроки возвратов</h4>
                        <div>
                            <?php foreach ($filters['in_work_return'] AS $key => $one) { ?>
                                <p><a href="<?=URL::site('admin/orders/items');?>?ids=<?=implode(',', $one['ids'])?>">
                                        <span style="background: <?=$one['color']?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                        <?=($one['label'] . '('.count($one['ids']).')')?>
                                    </a></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (ORM::factory('Permission')->checkPermission('synchronization_states')): ?>
                <a href="<?=URL::site('admin/orders/synchronization');?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Синхронизировать статусы с техномиром</a><br /><br />
            <?php endif ?>

        </div>
    </div>

    <br /><br />
    <? if(ORM::factory('Permission')->checkPermission('orders_edit_salary')) { ?>
        <a href="#" class="btn btn-primary" id="salary_ok"><i class="icon-white icon-thumbs-up"></i> З/п выплаченна</a>
        <?php if($button_status == 1):?>
            <?php $url = $_SERVER['REQUEST_URI']."&status=1"; ?>
            <a href="<?= URL::site($url); ?>" class="btn btn-primary" ><i class="icon-white icon-thumbs-up"></i> Выплатить всю З/п</a>
        <?php endif ?>
    <?php } ?>

    <!--	<a href="#bind_supp_order" role="button" class="btn btn-primary" data-toggle="modal"><i class="icon-white icon-retweet"></i>Привязать к заказу поставщика</a> <br/>-->
    <div id="bind_supp_order" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>Привязка позиций к заказу поставщика</h3>
        </div>
        <div class="modal-body">
            <input type="text" class="input-block-level inputTTN" placeholder="Укажите номер заказа постащика" required>
            <input type="text" class="input-block-level datepicker inputTTN" placeholder="Укажите дату в накладной" required>
            <input type="hidden" class="orderitems_ids">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Отмена</button>
            <button class="btn btn-primary submitTTN" data-dismiss="modal" disabled="disabled">Отправить</button>
        </div>
    </div>

    <?php if($orderitems): ?>
        <?php if (ORM::factory('Permission')->checkPermission('give_all_order') AND ($orderitems[0]->order->ready_order == 1 OR $orderitems[0]->order->ready_order == 2)): ?>
            <a href="<?=URL::site('/admin/orders/give_all_position?order_id='.$orderitems[0]->order->id);?>" class="btn btn-primary"><i class="icon-white icon-refresh"></i> Выдать весь заказ</a><br /><br />
        <?php endif ?>
    <?php endif ?>
    <br />
    <br />

    <?php if (!empty($new_result_group_by)): ?>
        <div class="well">
            <?php if(!empty($_GET['state_id'])){$state_id = $_GET['state_id'];} $count1 = (integer)count($new_result_group_by)/2; $count2 =count($new_result_group_by)-$count1; ?>
            <?php if(!empty($_GET['state_id'])):?>
                <?php foreach ($new_result_group_by AS $key => $one) { ?>
                    <a href="<?=URL::site('admin/orders/items');?>?state_id=<?=$state_id;?>&supplier_id=<?=$key;?>" class="popover_link" rel="popover" data-placement="bottom" data-content="<?=addcslashes($one[0]."<br>Заказ до: ".$one[2], '"')?>"><?= $one[0]." (".$one[1]."шт. Заказ до: ".$one[2]."), " ?></a>
                <?php } ?>
            <?php endif; ?>
            <?php if(!empty($_GET['ids'])):?>
                <?php foreach ($new_result_group_by AS $key => $one) { ?>
                    <a href="<?=URL::site('admin/orders/items');?>?ids=<?=$one[2];?>&supplier_id=<?=$key;?>"><span><?= $one[0]." (".$one[1]."), " ?></span></a>
                <?php } ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>


    <div class="table_admin">
        <?php if(count($orderitems)==0): ?>
            <span style="color:red;"><?php  echo $massagePermission; ?></span>
        <?php endif; ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>№</th>
                <th><input type="checkbox" id="select_all" /></th>
                <th><?=Utils::order_by($order_by, "order.id", "№ заказа")?></th>
                <th><?=Utils::order_by($order_by, "order.date_time", "Дата")?></th>
                <th>Клиент</th>
                <th style="width: 200px">Состояние</th>
                <th>Доп. данные</th>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_supplier')) { ?>
                    <th>Поставщик</th>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_purchase')) { ?>
                    <th>Закупка</th>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_sale')) { ?>
                    <th>Продажа</th>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_delivery')) { ?>
                    <th>Доставка от поставщика</th>
                <?php } ?>
                <th>Ожидается</th>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
                    <th><?=Utils::order_by($order_by, "order.manager_id", "Менеджер")?></th>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_salary')) { ?>
                    <th>Зарплата</th>
                <?php } ?>
                <th>Баланс общ/заказ</th>
                <th>Архив</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <?php $i = 0?>
            <?php foreach($orderitems as $orderitem): ?>
                <?php if ($orderitem->state_id == 1 AND $is_buyer) continue;?>
                <?php
                $i++;
                $styles = "";
                if(!empty($orderitem->state->bg_color)) {
                    $styles .= "background: ".$orderitem->state->bg_color."; ";
                }
                if(!empty($orderitem->state->bg_color)) {
                    $styles .= "color: ".$orderitem->state->font_color."; ";
                }
                ?>
                <tr>
                    <td style="<?=$styles?>"><?=$i + ($page-1) * 30;?></td>
                    <td style="<?=$styles?>"><input type="checkbox" class="order_checkbox" data-id="<?=$orderitem->id?>" /></td>
                    <td style="<?=$styles?>">
                        <a href="<?=URL::site('admin/orders/items/'.$orderitem->order->id);?>"><?=$orderitem->order->get_order_number()?></a>
                        <br /><br />
                        <?php if(!in_array ($orderitem->id, $orderitem_supplier) AND (in_array ($orderitem->state_id, [2,6,8,19,31,32]))):?>
                            <a href="#supplier_order<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Ввести номер заказа поставщика</a>
                            <!-- Modal -->
                            <div id="supplier_order<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h3>Привязка к номеру заказа поставщика</h3>
                                </div>
                                <div class="modal-body">
                                    <?= Form::open(URL::site('admin/orders/tie'), array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>

                                    <div class="control-group">
                                        <?= Form::label('created_supplier_order_id', 'Существующий номер заказа поставцика', array('class' => 'control-label')); ?>
                                        <div class="controls">
                                            <?= Form::select('created_supplier_order_id', $created_orders_supplier[$orderitem->supplier_id], Arr::get($data, 'created_supplier_order_id')); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <?= Form::label('supplier_order', 'Создать номер заказа поставцика', array('class' => 'control-label')); ?>
                                        <div class="controls">
                                            <?= Form::input('supplier_order', HTML::chars(Arr::get($data, 'supplier_order'))); ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <?= Form::label('date_supp_order', 'Укажите дату в накладной (если создается новый)', array('class' => 'control-label')); ?>
                                        <div class="controls">
                                            <?= Form::input('date_supp_order', HTML::chars(Arr::get($data, 'date_supp_order')), array('class' => 'datepicker')); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <?= Form::label('supplier_order_payment', 'Проплата водителю', array('class' => 'control-label')); ?>
                                        <div class="controls">
                                            <?= Form::input('supplier_order_payment', HTML::chars(Arr::get($data, 'supplier_order_payment'))); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <?= Form::label('supplier_order_delivery', 'Сумма доставки', array('class' => 'control-label')); ?>
                                        <div class="controls">
                                            <?= Form::input('supplier_order_delivery', HTML::chars(Arr::get($data, 'supplier_order_delivery'))); ?>
                                        </div>
                                    </div>

                                    <?php if($orderitem->supplier->currency->ratio != 1): ?>
                                        <div class="control-group">
                                            <?= Form::label('rate', 'Курс', array('class' => 'control-label')); ?>
                                            <div class="controls">
                                                <?= Form::input('rate', $orderitem->supplier->currency->ratio, array('validate' => 'float', 'type' => 'number', 'step' => '0.01')); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="control-group">
                                        <div class="controls">
                                            <?= Form::hidden('orderitem_id', $orderitem->id , array('validate' => 'required')); ?>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <?= Form::hidden('supp_id', $orderitem->supplier_id , array('validate' => 'required')); ?>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary save_item', 'disabled' => 'disabled')); ?>
                                        </div>
                                    </div>
                                    <?= Form::close(); ?>
                                </div>
                            </div>
                            <!--END Modal -->
                        <?php endif;?>
                    </td>
                    <td style="<?=$styles?>"><?php $d = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
                    <td style="<?=$styles?>"><?=$orderitem->order->client->name?> <?=$orderitem->order->client->surname?><br><?=$orderitem->order->client->phone?></td>
                    <?php $var_id = Auth::instance()->get_user()->id; ?>
                    <td style="min-width:140px; <?=$styles?>"<? if((ORM::factory('Permission')->checkPermission('orders_edit_state'))&&( !in_array($orderitem->state_id, [4,14,15,17]))) {
                        if($orderitem->salary == 1){
                            if($var_id==2 or $var_id==74){?>
                                class="editable_state" id="state_<?=$orderitem->id?>"
                            <?php }
                        } else{ ?>
                            class="editable_state" id="state_<?=$orderitem->id?>"
                        <?php }
                    } ?>>
                        <?php if ($orderitem->logs->find_all()->as_array()) { $count = count(array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7)); $j=0;?>
                            <?php foreach (array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7) AS $one) {
                                $j++;
                                if ($j == 1)
                                {
                                    echo '<p><img src="'.URL::base().'media/img/states/'.$one->state->img.'" title="'.$one->state->description.'" /> <span class="orderitem_state" title="'.$one->state->name.'">'. date('d.m.Y H:i', strtotime($one->date_time)).'</span></p>';?>

                                <?php }  } ?>

                             <?php if(ORM::factory('Permission')->checkPermission('add_button_pack') && in_array($orderitem->state_id, [3])): ?>
                                <a id="orderitem_log_pack" href="#orderitem_log_pack<?=$orderitem->id?>" role="button" class="btn btn-mini" type="button">Товар упакован</a>
                             <?php endif; ?>

                            <br><br>
                            <a href="#orderitem_log<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>
                            <br>

                            <!-- Modal -->
                            <div id="orderitem_log<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h3>История изменения статусов</h3>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <th>Состояние</th>
                                            <th>Время</th>
                                            <th>Пользователь</th>
                                            <th>Синхронизация с техномиром</th>
                                        </tr>
                                        <?php foreach ($array = $orderitem->logs->find_all()->as_array() AS $one) { ?>
                                            <tr <?=($one == end($array) ? 'class="success"' : '')?>>
                                                <td><img src="<?=URL::base()?>media/img/states/<?=$one->state->img?>" title="<?=$one->state->description?>" /> <?=$one->state->name?></td>
                                                <td><?=date('d.m.Y H:i:s', strtotime($one->date_time))?></td>
                                                <td><?=$one->user->surname?></td>
                                                <td><?= $one->tehnomir==1 ? "Да" : "Нет" ?></td>
                                            </tr>
                                        <?php } ?>

                                    </table>
                                </div>
                            </div>
                            <!--comment for order position -->
                            <!--END Modal -->
                            <br>
                                <a href="#orderitem_log_comit<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Комментарий состояния позиции заказа</a>

                            <!-- Modal -->
                            <div id="orderitem_log_comit<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h3>Комментарий позиции заказа</h3>
                                </div>

                                <div class="modal-body">
                                    <form class="form_orderitem_log_comit" class="form" >
                                        <div class="control-group">

                                            <h4>Комментарий</h4>
                                            <input type="hidden" name="order_item_id" value="<?=$orderitem->id?>">
                                            <input type="hidden" name="author_id" value="<?=$var_id?>">
                                            <div class="well">
                                                <table>
                                                    <tr>
                                                        <th>Дата</th>
                                                        <th>Комментарий</th>
                                                        <th>Пользователь</th>
                                                    </tr>
                                                    <?php foreach($orderitem->comments->find_all() as $comment ):?>
                                                        <tr>
                                                            <td><?php echo $comment->created_date; ?></td>
                                                            <td><?php echo $comment->comment; ?></td>
                                                            <td><?php echo $comment->author->name; ?> <?php echo $comment->author->surname; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </table>
                                            </div>
                                            <? if(ORM::factory('Permission')->checkPermission('add_stock_comment')) { ?>
                                                <div class="controls">
                                                    <textarea name="comment" class="" style="width:97%;" placeholder="Введите текст" rows="3"></textarea>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <? if(ORM::factory('Permission')->checkPermission('add_stock_comment')) { ?>
                                            <div class="control-group">
                                                <?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary ')); ?>
                                            </div>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>
                            <!--END Modal -->

                        <?php } else { ?>
                            <img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
                        <?php } ?>

                        <?php if (($orderitem->confirmed == 0 AND in_array($orderitem->state->text_id, array('withdrawal', 'not_available')))
                            AND ((ORM::factory('Permission')->checkRole('manager') AND $orderitem->order->manager_id = Auth::instance()->get_user()->id)
                                OR (ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Руководитель отделения продаж') OR ORM::factory('Permission')->checkRole('Програмист') OR ORM::factory('Permission')->checkRole('Менеджер по Выдаче') OR ORM::factory('Permission')->checkRole('Директор') ))) { ?>
                            <div class="alert alert-block">
                                <h4>Внимание!</h4>
                                Статус позиции изменен на "<?=$orderitem->state->name?>"
                                <button class="btn btn-success confirm_orderitem" data-orderitem_id="<?=$orderitem->id?>" type="button">Ознакомлен</button>
                            </div>

                        <?php } ?>

                    </td>
                    <td style="<?=$styles?>">
                        <b>Артикул:</b> <?=$orderitem->article?> (<?php if($orderitem->amount > 1) :?><b style="font-weight: bold; background: white; color: red; "><?=$orderitem->amount?>шт</b><?php else :?><b><?=$orderitem->amount?>шт</b><?php endif;?>)<br />
                        <b>Производитель:</b> <?=$orderitem->brand?><br />
                        <b><?php $short_name = $orderitem->name; $words=explode(" ",$short_name); echo implode(" ",array_splice($words,0,2)) ?></b><br />
                        <b>Ко-рий менеджера:</b> <?= empty($orderitem->manager_comment) ? $orderitem->order->manager_comment :  $orderitem->manager_comment?><br />
                        <b>Ожидание:</b> <?= $orderitem->delivery_days?>

                        <a href="#orderitem_more_info<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>

                        <!-- Modal -->
                        <div id="orderitem_more_info<?=$orderitem->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <!--							<h3>История изменения статусов</h3>-->
                            </div>
                            <div class="modal-body">
                                <b>Наименование:</b> <?=$orderitem->name?><br />
                                <b>Ожидание:</b> <?=$orderitem->delivery_days?><br />
                                <?php if($orderitem->amount > 1) :?>
                                    <b style="font-weight: bold; background: white; color: red; ">Кол-во: <?=$orderitem->amount?><br /></b>
                                <?php else :?>
                                    <b>Кол-во: <?=$orderitem->amount?><br /></b>
                                <?php endif;?>
                                <b>Онлайн заказ:</b><?=($orderitem->order->online == 1 ? "Да" : "Нет")?><br />
                                <?php if(!empty($orderitem->discount->id)) { ?><b>Уровень цен:</b> <?=$orderitem->discount->name?><br /><?php } ?>
                                <?=(!empty($orderitem->order->client->comment) ? "<b>Комментарий к клиенту:</b> ".$orderitem->order->client->comment."<br />" : "")?>
                                <b>Ко-рий менеджера:</b> <?= empty($orderitem->manager_comment) ? $orderitem->order->manager_comment :  $orderitem->manager_comment?><br />
                                <b>Номер заказа постащика:</b> <?= isset($orderitem->supp_order->supplier_order->order_supplier) ? $orderitem->supp_order->supplier_order->order_supplier :  ''?><br />
                            </div>
                        </div>

                        <?/*=(!empty($orderitem->order->manager_comment) ? "<b>Комментарий менеджера:</b> ".$orderitem->order->manager_comment."<br />" : "")?>
							<?=(!empty($orderitem->order->client_comment) ? "<b>Комментарий клиента:</b> ".$orderitem->order->client_comment."<br />" : "")*/?>
                    </td>
                    <? if(ORM::factory('Permission')->checkPermission('orders_show_supplier')) { ?>
                        <td style="<?=$styles?>">
                            <?php if(ORM::factory('Permission')->checkRole('manager') || ORM::factory('Permission')->checkRole('Руководитель отделения продаж')): ?>
                                <?=$orderitem->supplier->id?>
                            <?php else :?>
                                <?=$orderitem->supplier->name?>
                            <?php endif; ?>
                            <br>----------<br>
                            <?=$orderitem->supplier->notice?>
                        </td>
                    <?php } ?>
                    <? if(ORM::factory('Permission')->checkPermission('orders_show_purchase')) { ?>
                        <td style="<?=$styles?>"><?=$orderitem->purchase_per_unit*$orderitem->amount?> грн.
                        <?php if($orderitem->purchase_per_unit_in_currency && $orderitem->currency->code != 'UAH'): ?>
                            <br><b>В валюте:</b><br>
                            <?=$orderitem->purchase_per_unit_in_currency*$orderitem->amount?> <?=$orderitem->currency->code?></td>
                        <?php endif; ?>
                    <?php } ?>
                    <? if(ORM::factory('Permission')->checkPermission('orders_show_sale')) { ?>
                        <td style="<?=$styles?>"><?=$orderitem->sale_per_unit*$orderitem->amount?> грн.</td>
                    <?php } ?>
                    <? if(ORM::factory('Permission')->checkPermission('orders_show_delivery')) { ?>
                        <td style="<?=$styles?>"><?=$orderitem->delivery_price?> грн.</td>
                    <?php } ?>
                    <td style="<?=$styles?>">
                        <?php
                        if ($orderitem->state_id == 2 OR $orderitem->state_id == 6 OR $orderitem->state_id == 8 OR $orderitem->state_id == 31) {
                            $order_date = $d;
                            $delivery_days = $orderitem->delivery_days;

                            if ($orderitem->supplier->order_to) {
                                $order_to = $orderitem->supplier->order_to; //str_replace('.', ':', $orderitem->supplier->order_to);
//                                echo "<br />".date('H:i', strtotime($orderitem->supplier->order_to))."<br />";
//                                echo "<br />".date('H:i', strtotime($order_to))."<br />";
                                if ($order_date->format('H:i') < date('H:i', strtotime($order_to))) {
                                    $delivery_days--;
                                }
                            }

                            $order_date->modify('+' . $delivery_days . 'days');

                            echo '<b>Ожидается:</b> <span class="expected" data-date="' . $order_date->format('Y-m-d') . '">' . $order_date->format('d.m.Y') . '</span>';
//							echo "<br />".$order_date->format('H:i')."<br />";

                        }
                        ?>
                        <?php
                        if ($orderitem->state_id == 13)
                        {
                            $order_date_return_max = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time);
                            $order_date_return_normal = clone $order_date_return_max;
                            $order_date_return_normal->add(new DateInterval('P10D'));
                            $order_date_return_max->add(new DateInterval('P14D'));
                            echo '<b>Максимальный срок возврата:</b> <span class="expected_return" data-midle="' . $order_date_return_normal->format('Y-m-d') . '" data-max="' . $order_date_return_max->format('Y-m-d') . '">' . $order_date_return_max->format('d.m.Y') . '</span>';
                            //						echo "<br>".$order_date_return_normal->format('Y-m-d');
                        }

                        ?>

                    </td>
                    <? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
                        <?php if ($orderitem->order->manager->id != Auth::instance()->get_user()->id) $show_summary = false;  ?>
                        <td style="<?=$styles?>">
                            <?=$orderitem->order->manager->surname?>
                            <br><br>
                            <?php if (ORM::factory('Permission')->checkPermission('add_order_tm') AND isset($orderitem_tm_array)) : ?>
                                <?php if ($orderitem->supplier_id == 38) :?>
                                    <?php
                                    $d = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time);
                                    $d->format('d.m.Y H:i:s');
                                    $date1 = new DateTime("yesterday");
                                    $date1->format('d.m.Y H:i:s');
                                    ?>
                                    <?php if ( !in_array ($orderitem->id, $orderitem_tm_array) AND ($orderitem->state_id == 16 OR $orderitem->state_id == 7)) : ?>
                                        <a role="button" data-idorder="<?=$orderitem->id?>" role="button" id="" class="btn btn-mini create_tm" data-toggle="modal">Оформить заказ позиции на техномире</a>
                                    <?php endif; ?>
                                <?php endif;?>
                            <?php endif;?>
                        </td>
                    <?php } ?>
                    <? if(ORM::factory('Permission')->checkPermission('orders_show_salary')) { ?>
                        <td style="<?=$styles?>">
                            <?php
                            if($orderitem->salary == 1) echo "<strike>";
                            foreach($orderitem->salary_arr as $s) {
                                if($s['dont_show_salary'] == 1) continue;
                                echo "<b>".$s['name']."</b>: ".$s['value']." грн.<br />";
                            }
                            if($orderitem->salary == 1) echo "</strike>";
                            ?>
                        </td>
                    <?php } ?>
                    <td style="<?=$styles?>">
                        <? $balance = $orderitem->order->get_balance(); ?>
                        <span style="<?=(($clients_on_page[$orderitem->order->client_id]['active_balance'] < 0) ? "color: #FF6666 !important;" : "color: #00AA00 !important;")?> font-weight: bold;">
								<?=$clients_on_page[$orderitem->order->client_id]['active_balance']?>
							</span> / <span style="<?=(($balance['balance'] < 0) ? "color: #FF6666 !important;" : "color: #00AA00 !important;")?> font-weight: bold;"><?=$balance['balance']?></span>
                    </td>
                    <td style="<?=$styles?>"><?=($orderitem->order->archive == 1 ? "Да" : "Нет")?></td>
                    <td style="<?=$styles?>">
                        <?php if ((ORM::factory('Permission')->checkPermission('orders_show_manager')) AND (ORM::factory('Orderitem')->where('id', '=', $orderitem->id)->and_where('state_id', '=', 1)->count_all())) { ?>
                            <a title="Отправить в закупку" alt="Отправить в закупку" data-item_id="<?=$orderitem->id;?>" class="btn btn-mini to_purchase"><i class="icon-tasks"></i></a>
                        <?php } ?>
                        <a class="btn btn-mini" href="<?=URL::site('admin/orders/edit_item/'.$orderitem->id);?>"><i class="icon-edit"></i></a>

                        <? if(ORM::factory('Permission')->checkPermission('orders_delete_orderitem')) { ?>
                            <a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/orders/delete_item/'.$orderitem->id);?>"><i class="icon-remove"></i></a>
                        <?php } ?>
                        <?php if((($orderitem->state_id == 3 AND $orderitem->order->delivery_method_id != 3) OR ($orderitem->state_id == 37 AND $orderitem->order->delivery_method_id == 3)) AND $orderitem->order->np_area_id AND $orderitem->order->np_city_id AND $orderitem->order->np_warehouse_id): ?>
                            <a href="/admin/orders/create_express/<?=$orderitem->id ?>" title="Накладная" class="btn btn-mini"><i class="icon-file"></i></a>
                        <?php endif; ?>

                        <?php if($orderitem->ttn->ref): ?>
                            <a class="create_ttn_doc" href="#" data-id="<?=$orderitem->id ?>" data-url="/admin/ajax/create_ttn_doc" title="Накладная" class="btn btn-mini"><i class="icon-file"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <td></td>
                <td><b>Общая сумма<br>
                        (на всех страницах!!!):</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_supplier')) { ?>
                    <td></td>
                <?php } ?>
                <td>
                    <?php if (ORM::factory('Permission')->checkPermission('orders_show_purchase') AND !ORM::factory('Permission')->checkRole('manager') OR (ORM::factory('Permission')->checkRole('manager') AND $show_summary)) { ?>
                        <?=$total['purchase']?> грн.
                    <?php } ?>
                </td>
                <td>
                    <?php if (ORM::factory('Permission')->checkPermission('orders_show_sale') AND !ORM::factory('Permission')->checkRole('manager') OR (ORM::factory('Permission')->checkRole('manager') AND $show_summary)) { ?>
                        <?=$total['sale']?> грн.
                    <?php } ?>
                </td>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_delivery')) { ?>
                    <td><?=$total['delivery']?> грн.</td>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
                    <td></td>
                <?php } ?>
                <? if(ORM::factory('Permission')->checkPermission('orders_show_salary')) { ?>
                    <td></td>
                <?php } ?>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
    <?=$pagination?>


    <?php
    $all_sullary = 0;
    foreach ($salary as $user_id => $s) {
        //if (isset($penalty[$user_id]))
        if ($s['dont_show_salary'] == 1) continue;
        echo "<div style='width: 50%; float: left'><b>" . $s['name'] . "</b>: (оборот: ".$s['circulation']."грн) " . $s['value'];
        $all_sullary += $s['value'];

        if($user_id == 2 && in_array(Auth::instance()->get_user()->id, [2, 3, 74, 167])){ ?>
            <a href="https://eparts.kiev.ua/admin/costs/personal_costs">- <?= $totalCost; ?></a><a href="https://eparts.kiev.ua/admin/costs/list">- <?= $totalCostAll; ?></a> = <?= $s['value'] - $totalCost - $totalCostAll ?>
        <?php }


        if (isset($penalty[$user_id])) { ?>
            <!-- MODAL WINDOW -->
            <div id="penalty_<?= $user_id ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4><?= $s['name'] ?>. Штрафы и выплаты</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Пользователь</th>
                            <th>Дата/время</th>
                            <th>Заказ</th>
                            <th>Сумма (грн)</th>
                            <th>Причина</th>
                        </tr>
                        <?php $total = 0; ?>
                        <?php foreach ($penalty[$user_id] AS $one) { ?>
                            <tr>
                                <td><?= isset($one->user_id) ? ORM::factory('user')->where('id', '=', $one->user_id)->find()->surname : '-' ?></td>
                                <td><?= date('d.m.Y H:i:s', strtotime($one->date)) ?></td>
                                <td>
                                    <a href="<?= URL::site('admin/orders/items/' . $one->order_id); ?>"><?= $one->order_id ?>
                                </td>
                                <td><?= $one->amount ?></td>
                                <td><?= $one->description ?></td>
                            </tr>

                            <?php $total += $one->amount; ?>
                        <?php } ?>
                        <tr>
                            <td colspan="3"><b>Сумма:</b></td>
                            <td><b><?= $total ?></b></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- END WINDOW -->

            <a href="#penalty_<?= $user_id ?>" title="Штраф" role="button"
               data-toggle="modal">- <?= $total ?></a> = <?= $s['value'] - $total ?>

        <?php }
        echo " грн <br /></div>";

//SECOND block

        if ($s['dont_show_salary'] == 1) continue;
        echo "<div style='width: 50%; float: left'><b>" . $s['name'] . "</b>: " . $s['value2'];

        if (isset($penalty2[$user_id])) { ?>
            <!-- MODAL WINDOW -->
            <div id="penalty2_<?= $user_id ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4><?= $s['name'] ?>. Штрафы и выплаты</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Пользователь</th>
                            <th>Дата/время</th>
                            <th>Заказ</th>
                            <th>Сумма (грн)</th>
                            <th>Причина</th>
                        </tr>
                        <?php $total2 = 0; ?>
                        <?php foreach ($penalty2[$user_id] AS $one2) { ?>
                            <tr>
                                <td><?= isset($one2->user_id) ? ORM::factory('user')->where('id', '=', $one2->user_id)->find()->surname : '-' ?></td>
                                <td><?= date('d.m.Y H:i:s', strtotime($one2->date)) ?></td>
                                <td>
                                    <a href="<?= URL::site('admin/orders/items/' . $one2->order_id); ?>"><?= $one2->order_id ?>
                                </td>
                                <td><?= $one2->amount ?></td>
                                <td><?= $one2->description ?></td>
                            </tr>

                            <?php $total2 += $one2->amount; ?>
                        <?php } ?>
                        <tr>
                            <td colspan="3"><b>Сумма:</b></td>
                            <td><b><?= $total2 ?></b></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- END WINDOW -->

            <a href="#penalty2_<?= $user_id ?>" title="Штраф" role="button"
               data-toggle="modal">- <?= $total2 ?></a> = <?= $s['value2'] - $total2 ?>

        <?php }
        echo " грн <br /></div>";

    }
    ?>

    <div style="width: 100%; height: 2px; background: black; float: left;" ></div>
    Всего: <?= $all_sullary; ?> грн.
</div>