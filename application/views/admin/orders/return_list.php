<script>
    var print_url = '<?=URL::site('admin/orders/print_items');?>';
    var sticker_print_url = '<?=URL::site('admin/orders/print_sticker_items');?>';
    var edit_state_url = '<?=URL::site('admin/orders/edit_item_state');?>';
    var save_state_url = '<?=URL::site('admin/orders/save_item_state');?>';
    var salary_ok_url = '<?=URL::site('admin/orders/salary_ok');?>';
    var move_items_url = '<?=URL::site('admin/orders/move_items');?>';
</script>


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
    <?php foreach($orderitems as $orderitem) : ?>
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
            <td><?=$i + ($page-1) * 30;?></td>
            <td style="<?=$styles?>"><input type="checkbox" class="order_checkbox" data-id="<?=$orderitem->id?>" /></td>
            <td style="<?=$styles?>"><a href="<?=URL::site('admin/orders/items/'.$orderitem->order->id);?>"><?=$orderitem->order->get_order_number()?></a></td>
            <td style="<?=$styles?>"><?php $d = new DateTime($orderitem->date_time ? $orderitem->date_time : $orderitem->order->date_time); ?><?=$d->format('d.m.Y H:i:s')?></td>
            <td style="<?=$styles?>"><?=$orderitem->order->client->name?> <?=$orderitem->order->client->surname?><br><?=$orderitem->order->client->phone?></td>
            <td style="min-width:140px; <?=$styles?>"<? if(ORM::factory('Permission')->checkPermission('orders_edit_state')) { ?> class="editable_state" id="state_<?=$orderitem->id?>"<?php } ?>>
                <?php if ($orderitem->logs->find_all()->as_array()) { ?>
                    <?php foreach (array_slice($array = $orderitem->logs->order_by('date_time', 'DESC')->find_all()->as_array(),0,7) AS $one) {
                        echo '<p><img src="'.URL::base().'media/img/states/'.$one->state->img.'" title="'.$one->state->description.'" /> <span class="orderitem_state" title="'.$one->state->name.'">'. date('d.m.Y H:i', strtotime($one->date_time)).'</span></p>';?>
                    <?php } ?>
                    <a href="#orderitem_log<?=$orderitem->id?>" role="button" class="btn btn-mini" data-toggle="modal">Подробнее..</a>

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
                                </tr>
                                <?php foreach ($array = $orderitem->logs->find_all()->as_array() AS $one) { ?>
                                    <tr <?=($one == end($array) ? 'class="success"' : '')?>>
                                        <td><img src="<?=URL::base()?>media/img/states/<?=$one->state->img?>" title="<?=$one->state->description?>" /> <?=$one->state->name?></td>
                                        <td><?=date('d.m.Y H:i:s', strtotime($one->date_time))?></td>
                                        <td><?=$one->user->surname?></td>
                                    </tr>
                                <?php } ?>

                            </table>
                        </div>
                    </div>
                <?php } else { ?>
                    <img src="<?=URL::base()?>media/img/states/<?=$orderitem->state->img?>" title="<?=$orderitem->state->description?>" /> <?=$orderitem->state->name?>
                <?php } ?>


                <?php if (($orderitem->confirmed == 0 AND in_array($orderitem->state->text_id, array('withdrawal', 'not_available')))
                    AND ((ORM::factory('Permission')->checkRole('manager') AND $orderitem->order->manager_id = Auth::instance()->get_user()->id)
                        OR (ORM::factory('Permission')->checkRole('Владелец') OR ORM::factory('Permission')->checkRole('Руководитель отделения продаж')))) { ?>
                    <div class="alert alert-block">
                        <h4>Внимание!</h4>
                        Статус позиции изменен на "<?=$orderitem->state->name?>"
                        <button class="btn btn-success confirm_orderitem" data-orderitem_id="<?=$orderitem->id?>" type="button">Ознакомлен</button>
                    </div>

                <?php } ?>

            </td>
            <td style="<?=$styles?>">
                <b>Артикул:</b> <?=$orderitem->article?><br />
                <b>Производитель:</b> <?=$orderitem->brand?><br />
                <b>Наименование:</b> <?=$orderitem->name?><br />
                <b>Ожидание:</b> <?=$orderitem->delivery_days?><br />
                <b>Кол-во:</b> <?=$orderitem->amount?><br />
                <b>Онлайн заказ:</b><?=($orderitem->order->online == 1 ? "Да" : "Нет")?><br />
                <?php if(!empty($orderitem->discount->id)) { ?><b>Уровень цен:</b> <?=$orderitem->discount->name?><br /><?php } ?>
                <?=(!empty($orderitem->order->client->comment) ? "<b>Комментарий к клиенту:</b> ".$orderitem->order->client->comment."<br />" : "")?>
                <?/*=(!empty($orderitem->order->manager_comment) ? "<b>Комментарий менеджера:</b> ".$orderitem->order->manager_comment."<br />" : "")?>
					<?=(!empty($orderitem->order->client_comment) ? "<b>Комментарий клиента:</b> ".$orderitem->order->client_comment."<br />" : "")*/?>
            </td>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_supplier')) { ?>
                <td style="<?=$styles?>"><?=$orderitem->supplier->name?></td>
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
                if ($orderitem->state_id == 2) {
                    $order_date = $d;
                    $delivery_days = $orderitem->delivery_days;

                    if ($orderitem->supplier->order_to) {
                        $order_to = str_replace('.', ':', $orderitem->supplier->order_to);
                        if ($order_date->format('H:i') < date('H:i', strtotime($order_to))) {
                            $delivery_days--;
                        }
                    }

                    $order_date->modify('+' . $delivery_days . 'days');
                    echo '<b>Ожидается:</b> <span class="expected" data-date="' . $order_date->format('Y-m-d') . '">' . $order_date->format('d.m.Y') . '</span>';
                }
                ?>

            </td>
            <? if(ORM::factory('Permission')->checkPermission('orders_show_manager')) { ?>
                <?php if ($orderitem->order->manager->id != Auth::instance()->get_user()->id) $show_summary = false;  ?>
                <td style="<?=$styles?>"><?=$orderitem->order->manager->surname?></td>
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
            <td style="">
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
<?=$pagination?>


<?php
foreach ($salary as $user_id => $s) {
    if (isset($penalty[$user_id]))
        if ($s['dont_show_salary'] == 1) continue;
    echo "<b>" . $s['name'] . "</b>: " . $s['value'];

    if (isset($penalty[$user_id])) { ?>

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

        <a href="#penalty_<?= $user_id ?>" title="Штраф" role="button"
           data-toggle="modal">- <?= $total ?></a> = <?= $s['value'] - $total ?>

    <?php }
    echo " грн <br />";
}
?>
</div>