<?php echo phpinfo();?>
<div class="container">
    <a class="btn btn-mini" href="<?= URL::site('admin/birthdaySetting/add/'); ?>"><i class="icon-plus"></i>
        Добавить</a><br/><br/>

    <?php if ($settings): ?>
        <table class="table table-striped table-bordered">
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Значение</th>
                <th></th>
            </tr>
            <?php foreach ($settings as $setting) : ?>
                <tr>
                    <td><?= $setting->id ?></td>
                    <td><?= $setting->name ?></td>
                    <td><?= $setting->desc ?></td>
                    <td><?= $setting->value ?></td>
                    <td>
                        <a class="btn btn-mini" href="<?= URL::site('admin/birthdaySetting/edit/' . $setting->id); ?>"><i
                                class="icon-edit"></i> Редактировать</a>
                        <a class="btn btn-mini btn-danger delete_row"
                           href="<?= URL::site('admin/birthdaySetting/delete/' . $setting->id); ?>"><i
                                class="icon-remove"></i> Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>