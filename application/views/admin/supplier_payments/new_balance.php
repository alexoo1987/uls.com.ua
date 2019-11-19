<div class="container">
    <div class="orders borded">
        <div class="flex_box">
            <?php foreach ($suppliers as $supplier): ?>
                <div class="in_flex_33">
                    <a href="<?=URL::site('admin/supplierpayment/balance_one?supplier_id='.$supplier->id);?>"><?= $supplier->name?></a>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>