
<div class="container">
    <?= Form::open('', array('class' => 'form-horizontal supplier_balance', 'method' => 'get')); ?>
        <div class="flex_box">
            <div class="in_flex_25">Дата от<br /><?= Form::input('date_from', HTML::chars(Arr::get($filters, 'date_from')), array('class' => 'datepicker dateFrom', 'data-from' => '2016-08-08')); ?> </div>
            <div class="in_flex_25">Дата до<br /><?= Form::input('date_to', HTML::chars(Arr::get($filters, 'date_to')), array('class' => 'datepicker')); ?></div>
            <div class="in_flex_25">Поставщик<br /><?= Form::select('supplier_id', $suppliers, Arr::get($filters, 'supplier_id')); ?><br /></div>
            <div class="in_flex_25">
                <br />
                <?= Form::submit('', 'Применить фильтр', array('class' => 'btn btn-primary white')); ?>
                <a href="<?=URL::site('admin/supplierpayment/list');?>" class="btn btn-primary white"><i class="icon-white icon-refresh"></i> Сброс</a>
            </div>
        </div>
    <?= Form::close(); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#order">Заказы</a></li>
        <li><a data-toggle="tab" href="#payments">Платежи</a></li>
        <li><a data-toggle="tab" href="#delivery">Доставки</a></li>
        <li><a data-toggle="tab" href="#returns">Возвраты</a></li>
        <li><a data-toggle="tab" href="#excel">Excel</a></li>
    </ul>

    <div class="tab-content">
        <div id="order" class="tab-pane fade in active">
            <div class="orders borded">
                <h3>Заказы</h3>
                <?php foreach ($supplier_balance_info['orders'] as $supplier_order): ?>
                    <?php $balance_order = 0; ?>
                    <div class="flex_box borded_bottom">
                        <div class="in_flex_80">
                            <div class="one_order">
                                <a data-toggle="collapse" data-target="#supp_order_<?= $supplier_order->id?>"><?= $supplier_order->order_supplier ?></a>
                                <div id="supp_order_<?= $supplier_order->id ?>" class="collapse">
                                    <div class="flex_box borded_bottom">
                                        <div class="in_flex_20">№ заказа у нас</div>
                                        <div class="in_flex_20">Артикул</div>
                                        <div class="in_flex_20">Бренд</div>
                                        <div class="in_flex_20">Менеджер</div>
                                        <div class="in_flex_20">Сумма</div>
                                    </div>
                                    <?php foreach ($supplier_order->orderitemssupplier->find_all()->as_array() as $one_position):?>
                                        <div class="flex_box borded_bottom">
                                            <div class="in_flex_20">
                                                <a href="<?=URL::site('admin/orders/items/'.$one_position->orderitem->order->id);?>"><?= $one_position->orderitem->order->id?></a>
                                            </div>
                                            <div class="in_flex_20"><?= $one_position->orderitem->article?></div>
                                            <div class="in_flex_20"><?= $one_position->orderitem->brand ?></div>
                                            <div class="in_flex_20"><?= $one_position->orderitem->order->manager->surname ?></div>
                                            <div class="in_flex_20"><?= $one_position->orderitem->amount*$one_position->orderitem->purchase_per_unit_in_currency." ".$supplier_balance_info['supplier']->currency->name ?></div>
                                        </div>
                                        <?php $balance_order += $one_position->orderitem->amount*$one_position->orderitem->purchase_per_unit_in_currency ?>
                                    <?php endforeach;?>
                                    <?php if( isset($supplier_order->supplierpay)): ?><p>Проплата: <?= $supplier_order->supplierpay->value  ?></p><?php endif; ?>
                                    <?php if( isset($supplier_order->supplierdelivery)): ?><p>Доставка: <?= $supplier_order->supplierdelivery->amount  ?></p><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="in_flex_10"><?=$supplier_order->date_time ?></div>
                        <div class="in_flex_10"><?= $balance_order." ".$supplier_balance_info['supplier']->currency->name ?></div>
                    </div>
                    <?php $total['orders'] += $balance_order; ?>
                <?php endforeach;?>
            </div>
        </div>
        <div id="payments" class="tab-pane fade">
            <div class="costs">
                <h3>Платежи</h3>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Поставщик</th>
                        <th>Значение</th>
                        <th>Курс</th>
                        <th>Дата</th>
                        <th>Сотрудник</th>
                        <th>Коментарий</th>
                        <th></th>
                    </tr>
                    <?php
                    foreach($supplier_balance_info['costs']  as $sp) : ?>
                        <tr>
                            <td><?=$sp->supplier->name?></td>
                            <td><?=($sp->ratio == 1 ? $sp->value : $sp->value . $sp->supplier->currency->code . ' / ' . round($sp->value*$sp->ratio))?>UAH</td>
                            <td><?= $sp->ratio ?></td>
                            <td><?php $d = new DateTime($sp->date_time); ?><?= $d->format('d.m.Y H:i:s') ?></td>
                            <td><?= $sp->user->surname ?></td>
                            <td><?=$sp->comment_text?></td>
                            <td>
                                <? if(ORM::factory('Permission')->checkPermission('supplierpayment_manage')) { ?>
                                    <a class="btn btn-mini btn-danger delete_row" href="<?=URL::site('admin/supplierpayment/delete/'.$sp->id);?>"><i class="icon-remove"></i> Удалить</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div id="delivery" class="tab-pane fade">
            <div class="delivery">
                <h3>Доставки</h3>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Поставщик</th>
                        <th>Значение</th>
                        <th>Дата</th>
                        <th>Сотрудник</th>
                        <th>Коментарий</th>
                    </tr>
                    <?php
                    foreach($supplier_balance_info['delivery']  as $del) : ?>
                        <tr>
                            <td><?=$del->supplier->name?></td>
                            <td><?=$del->amount ?> UAH</td>
                            <td><?php $d = new DateTime($del->created); ?><?= $d->format('d.m.Y H:i:s') ?></td>
                            <td><?= $del->user->surname ?></td>
                            <td><?=$del->comment?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
        <!--        --><?//= $del_pagination ?>
            </div>
        </div>
        <div id="returns" class="tab-pane fade">
            <div class="returns">
                <h3>Возвраты</h3>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Номер заказа</th>
                        <th>Артикул/Бренд</th>
                        <th>Кол-во</th>
                        <th>Дата возврата</th>
                        <th>Сотрудник</th>
                        <th>Цена в валюте поставщика</th>
                    </tr>
                    <?php
                    foreach($supplier_balance_info['returns']  as $return) : ?>
                        <tr>
                            <td><a href="<?=URL::site('admin/orders/items/'.$return->orderitem->order->id);?>"><?=$return->orderitem->order->id?></a></td>
                            <td><?=$return->orderitem->article." / ".$return->orderitem->brand?></td>
                            <td><?= $return->orderitem->amount ?></td>
                            <td><img src="<?=URL::base().'media/img/states/'.$return->state->img?>" title="<?=$return->state->description?>" /><?php $d = new DateTime($return->date_time); ?><?= $d->format('d.m.Y H:i:s') ?></td>
                            <td><?= $return->user->surname ?></td>
                            <td><?=$return->orderitem->purchase_per_unit_in_currency * $return->orderitem->amount?> <?= $supplier_balance_info['supplier']->currency->name ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><b>Итого:</b></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="excel" class="tab-pane fade">
            <div class="span6 offset3">
                <? if ($message) : ?>
                    <h3 class="alert alert-info">
                        <?= $message; ?>
                    </h3>
                <? endif; ?>

                <?= Form::open(URL::site('admin/supplierpayment/get_act_excel'), array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>

                <?= Form::hidden('date_from', $filters['date_from'] != '19.10.2017' ? $filters['date_from'] : '19.10.2017'); ?>

                <?= Form::hidden('date_to', $filters['date_to'] ? $filters['date_to'] : date("d.m.Y") ); ?>

                <?= Form::hidden('balance_before', serialize($total_before)); ?>

                <?= Form::hidden('balance_total', serialize($total)); ?>

                <?= Form::hidden('returns', serialize($supplier_balance_info['returns'])); ?>

                <?= Form::hidden('delivery', serialize($supplier_balance_info['delivery'])); ?>

                <?= Form::hidden('costs', serialize($supplier_balance_info['costs'])); ?>

                <?= Form::hidden('orders', serialize($supplier_balance_info['orders'])); ?>

                <?= Form::hidden('supplier', serialize($supplier_balance_info['supplier'])); ?>

                <div class="control-group">
                    <?= Form::label('variant', 'Вариант акта', array('class' => 'control-label')); ?>
                    <div class="controls">
                        <?= Form::select('variant', $variant, Arr::get($data, 'variant'), array('validate' => 'required')); ?>
                    </div>
                </div>

<!--                <div class="control-group">-->
<!--                    --><?//= Form::label('date_from', 'Дата от', array('class' => 'control-label')); ?>
<!--                    <div class="controls">-->
<!--                        --><?//= Form::input('date_from', '2017-09-01', array('class' => 'datepicker dateFrom', 'data-from' => '2017-09-01')); ?>
<!--                    </div>-->
<!--                </div>-->
<!---->
<!--                <div class="control-group">-->
<!--                    --><?//= Form::label('date_to', 'Дата до', array('class' => 'control-label')); ?>
<!--                    <div class="controls">-->
<!--                        --><?//= Form::input('date_to', HTML::chars(Arr::get($data, 'date_to')), array('class' => 'datepicker')); ?>
<!--                    </div>-->
<!--                </div>-->

                <div class="control-group">
                    <div class="controls">
                        <?= Form::submit('create', 'Получить', array('class' => 'btn btn-primary')); ?>
                    </div>
                </div>
                <?= Form::close(); ?>
            </div>
        </div>
    </div>

    <hr/>
    <?php $curr_name = $supplier_balance_info['supplier']->currency->code; ?>
    <?php $all_period_balance = [];?>
    <div class="flex_box">
        <div class="in_flex_33">
            <b>Сальдо до 19.10.2017:<br /></b><hr/>
            <?php foreach ($supplier_balance_info['supplier']->saldos->find_all()->as_array() as $saldo): ?>
                <?= $saldo->value." ".$saldo->currency->code."<br />" ?>
                <?php if(isset($all_period_balance[$saldo->currency->code]))
                {
                    $all_period_balance[$saldo->currency->code] += $saldo->value;
                }
                else{
                    $all_period_balance[$saldo->currency->code] = $saldo->value;
                }?>
            <?php endforeach; ?>
        </div>

        <?php if($filters['date_from'] != '19.10.2017'): ?>
            <div class="in_flex_33">
                <b>Баланс до [с 19.10.2017 - <?= $filters['date_from'] ?>):</b></br><hr/>
                Заказы:<?= $total_before['orders'] ?> <?= $curr_name ?></br>
                Доставки:<?= $total_before['delivery'] ?> UAH</br>
                Возвраты:<?= $total_before['returns'] ?> <?= $curr_name ?></br>
                Проплаты:<?= $total_before['payment'] ?> <?= $curr_name ?></br>
                <?php if(isset($all_period_balance[$curr_name]))
                {
                    $all_period_balance[$curr_name] += $total_before['payment'] + $total_before['returns'] - $total_before['orders'];
                }
                else{
                    $all_period_balance[$curr_name] = $total_before['payment'] + $total_before['returns'] - $total_before['orders'];
                }?>
            </div>
        <?php endif; ?>

        <div class="in_flex_33">
            <b>Баланс с <?= $filters['date_from'] ?> по <?= $filters['date_to'] ? $filters['date_to'] : date("d.m.y")  ?>: </b></br><hr/>
            Заказы:<?= $total['orders'] ?> <?= $curr_name ?></br>
            Доставки:<?= $total['delivery'] ?> UAH</br>
            Возвраты:<?= $total['returns'] ?> <?= $curr_name ?></br>
            Проплаты:<?= $total['payment'] ?> <?= $curr_name ?></br>
            <?php if(isset($all_period_balance[$curr_name]))
            {
                $all_period_balance[$curr_name] += $total['payment'] + $total['returns'] - $total['orders'];
            }
            else{
                $all_period_balance[$curr_name] = $total['payment'] + $total['returns'] - $total['orders'];
            }?>
           
        </div>

        <div class="in_flex_100">
            <hr/>
            Итого:<br />
            <?php foreach ($all_period_balance as $curr => $value): ?>
                <?= $value." ".$curr."<br />" ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>