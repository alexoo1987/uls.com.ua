<?php
if (empty($years)) {
    $years = range(1980, date('Y'));
}
$years_grouped = array();
foreach ($years as $year) {
    $group = floor($year / 10) * 10;
    if (!isset($years_grouped[$group])) $years_grouped[$group] = array();
    $years_grouped[$group][] = $year;
} ?>
<?php foreach ($years_grouped as $group => $years_list) { ?>
    <li>
        <ul class="select_car_year">
            <li><span class="dist"><?= $group ?>-е</span></li>
            <?php foreach ($years_list as $year): ?>
                <li><a href="#" class="select-creteria" data-info="<?= $year ?>"><?= $year ?></a></li>
            <?php endforeach; ?>
<!--            --><?//= str_repeat("<td></td>", 10 - count($years_list)) ?>
        </ul>
    </li>
<?php } ?>
