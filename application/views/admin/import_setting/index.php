<div class="container">
    <div class="row">
        <?php if (isset($_GET['run'])) { ?>
            <div class="alert alert-success">
                Планировщик запущен. За результатом загрузки следите <a href="<?= URL::site('admin/operations/list/'); ?>"><b>здесь</b></a>
            </div>
        <?php } ?>
        <a class="btn btn-success" href="<?= URL::site('admin/importSetting/create/'); ?>"><i
                class="icon-plus"></i> Создать</a>
        <a style="float: right" class="btn btn-warning" href="<?= URL::site('admin/importSetting/run/'); ?>"><i
                class="icon-refresh"></i> Принудительный запуск</a>

        <table class="table table-striped table-bordered">
            <tr>
                <th>Поставщик</th>
                <th>Настройки</th>
                <th>Последняя загрузка</th>
                <th>Комментарий</th>
                <th></th>
            </tr>
            <?php foreach ($data AS $row) { ?>
                <tr class="<?=$row->status ? 'success' : 'error'?>">
                    <td><?=$suppliers[$row->supplier_id] ?></td>
                    <td>
                        <?php $setting = json_decode($row->setting); ?>
                        <p><b>Дни недели:</b> <?= $setting->start->dayOfWeek ?></p>
                        <p><b>Часы:</b> <?= $setting->start->time ?></p>
                    </td>
                    <td><?=($row->last_date ? date('d.m.Y H:i:s', strtotime($row->last_date)) : '')?></td>
                    <td><?=$row->comment?></td>
                    <td>
                        <!-- Button to trigger modal -->
                        <a href="#log<?=$row->id?>" role="button" class="btn btn-mini" data-toggle="modal"><i
                                class="icon-download-alt"></i> Тех. информация</a>

                        <!-- Modal -->
                        <div id="log<?=$row->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3>Лог последней загрузки</h3>
                            </div>
                            <div class="modal-body">
                                <p><?=$row->log?></p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
                            </div>
                        </div>
                        <a class="btn btn-mini" href="<?= URL::site('admin/importSetting/edit/' . $row->id); ?>"><i
                                class="icon-edit"></i> Редактировать</a>
                        <?php $var_id = Auth::instance()->get_user()->id;
                        if($var_id==2 or $var_id==74){?>
                            <a class="btn btn-mini btn-danger delete_row"
                               href="<?= URL::site('admin/importSetting/delete/' . $row->id); ?>"><i
                                    class="icon-remove"></i> Удалить</a>
                        <?php } ?>

                    </td>
                </tr>
            <?php } ?>

        </table>
    </div>
</div>