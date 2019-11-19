<div class="container">
    <div class="orders borded">
        <div class="flex_box">
            <?php foreach ($piriods as $piriod): ?>
                <div class="in_flex_33">
                    <a href="<?=URL::site('admin/salary/manager_salary?month='.$piriod['month'].'&year='.$piriod['year']);?>"><?= "Месяц: ".$piriod['month']." ".$piriod['year'] ?></a>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>