<div class="container">
    <div class="span6 offset3">
        <?php if ($message) : ?>
            <h3 class="alert alert-info">
                <?= $message; ?>
            </h3>
        <? endif; ?>

        <?= Form::open('', array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>
        <div class="control-group">
            <?= Form::label('to_user', 'Кому', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::select('to_user', $users, false, array('validate' => 'required')); ?>
            </div>
        </div>

        <div class="control-group">
            <?= Form::label('amount', 'Сумма', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::input('amount', false, array('required' => 'required')); ?>
            </div>
        </div>

        <div class="control-group">
            <?= Form::label('comment', 'Комментарий', array('class' => 'control-label')); ?>
            <div class="controls">
                <?= Form::textarea('comment', false); ?>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <?= Form::submit('create', 'Сохранить', array('class' => 'btn btn-primary')); ?>
            </div>
        </div>
        <?= Form::close(); ?>
    </div>
</div>