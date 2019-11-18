<div class="container">
    <div class="row">
        <div class="span6 offset3">
            <? if ($message) : ?>
                <h3 class="alert alert-info">
                    <?= $message; ?>
                </h3>
            <? endif; ?>
            <?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form', 'autocomplete' => 'off')); ?>

            <div class="control-group">
                <?= Form::label('message', 'Текст сообщения', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::textarea('message', $text, array('readonly' => 'readonly', 'rows' => 5)); ?>
                </div>
            </div>
            <div class="control-group">
                <?= Form::label('amount', 'Сумма', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('amount', '', array('class' => 'amount', 'maxlength' => 13)); ?>
                </div>
            </div>
            <div class="control-group">
                <?= Form::label('number', 'Номер телефона', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('number', '+380', array('class' => 'phone', 'maxlength' => 13)); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <?= Form::submit('submit', 'Отправить', array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
            <?= Form::close(); ?>
        </div>
    </div>
</div>