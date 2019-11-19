<div class="container">
    <header class="page-header">
    <?php if (empty($data) || empty($signature)): ?>
        <h3>Нет активных заказов. Сформируйте новый заказ</h3>
    <?php else: ?>
        <?php if (!empty($partialPayment)): ?>
            <h1>Частичная оплата заказа #<?= $orderId ?></h1>
            <div>
                Перейти к полной <a href="<?= Helper_Url::createUrl('liqpay/order_pay/' . $orderId, [
                    'full' => 1
                ]) ?>">Оплате заказа</a>
            </div>
        <?php else: ?>
            <h1>Оплата заказа #<?= $orderId ?></h1>
        <?php endif; ?>

        <?php //if (isset($_GET['test']) && $_GET['test'] == '123'): ?>
        <form method="POST" action="<?= Libs_Liqpay::CHECKOUT_URL ?>" accept-charset="utf-8">
            <input type="hidden" name="data" value="<?= $data ?>" />
            <input type="hidden" name="signature" value="<?= $signature ?>" />
            <input type="image" src="//static.liqpay.ua/buttons/p1ru.radius.png" name="btn_text" />
        </form>
        <?php /*else: ?>
        <div id="liqpay_checkout"></div>
        <script>
            window.LiqPayCheckoutCallback = function() {
                LiqPayCheckout.init({
                    data: "<?= $data ?>",
                    signature: "<?= $signature ?>",
                    embedTo: "#liqpay_checkout",
                    mode: "embed" // embed || popup,
                }).on("liqpay.callback", function(data){
                    }).on("liqpay.ready", function(data){
                        // ready
                    }).on("liqpay.close", function(data){
                        // close
                });
            };
        </script>
        <script src="//static.liqpay.ua/libjs/checkout.js" async></script>
        <?php endif;*/
    endif; ?>
    </header>
</div>