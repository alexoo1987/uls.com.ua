<div class="container">
    <table class="table table-striped table-bordered">
        <tr>
            <th>ID</th>
            <th>Текст</th>
            <th>Показывать</th>
            <th></th>
        </tr>
        <?php foreach($modals as $modal) : ?>
            <tr>
                <td><?=$modal->id?></td>
                <td><?=$modal->text?></td>
                <td><?=$modal->active?></td>
                <td>
                    <a class="btn btn-mini" href="<?=URL::site('admin/settings/edit_modal/'.$modal->id);?>"><i class="icon-edit"></i> Редактировать</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

