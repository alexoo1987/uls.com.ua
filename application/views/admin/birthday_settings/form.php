<div class="container">
    <div class="row">
        <div class="span6 offset3">
            <? if ($message) : ?>
                <h3 class="alert alert-info">
                    <?= $message; ?>
                </h3>
            <? endif; ?>

            <?= Form::open('', array('class' => 'form-horizontal', )); ?>

            <div class="control-group">
                <?= Form::label('name', 'Название', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('name',  HTML::chars(Arr::get($data, 'name'))); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('desc', 'Описание', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('desc',  HTML::chars(Arr::get($data, 'desc'))); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('value', 'Значение', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::textarea('value', HTML::chars(Arr::get($data, 'value'))); ?>
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
</div>