<div class="container">
    <div class="row">
        <div class="span6 offset3">
            <? if ($message) : ?>
                <h3 class="alert alert-info">
                    <?= $message; ?>
                </h3>
            <? endif; ?>

            <?= Form::open(URL::site('admin/orders/get_act_excel'), array('class' => 'form-horizontal', 'id' => 'validate_form')); ?>

            <div class="control-group">
                <?= Form::label('variant', 'Вариант акта', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::select('variant', $variant, Arr::get($data, 'variant'), array('validate' => 'required')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('date_from', 'Дата от', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('date_from', '2016-08-08', array('class' => 'datepicker dateFrom', 'data-from' => '2016-08-08')); ?>
                </div>
            </div>

            <div class="control-group">
                <?= Form::label('date_to', 'Дата до', array('class' => 'control-label')); ?>
                <div class="controls">
                    <?= Form::input('date_to', HTML::chars(Arr::get($data, 'date_to')), array('class' => 'datepicker')); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <?= Form::submit('create', 'Получить', array('class' => 'btn btn-primary')); ?>
                </div>
            </div>
            <?= Form::close(); ?>
        </div>
    </div>
</div>


<!--<script type='text/javascript' src='https://apimgmtstorelinmtekiynqw.blob.core.windows.net/content/MediaLibrary/Widget/Tracking/dist/track.min.js'></script>-->

<!---->
<!---->
<!---->
<!--<div class="container">-->
<!--    <div class="panel panel-default">-->
<!--        <div class="panel-heading">-->
<!--            <button type="button" class="btn btn-default btn-xs spoiler-trigger" data-toggle="collapse">Toggle Button</button>-->
<!--            <table><tr><td>asdasdasd</td><td>asdasd</td></tr></table>-->
<!--        </div>-->
<!--        <div class="panel-collapse collapse out">-->
<!--            <div class="panel-body">-->
<!--                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sint, quos, accusamus. Quidem, molestiae. Ipsam consequatur impedit voluptatem, quod qui perferendis fugiat. Eos adipisci dolorem doloremque quos debitis excepturi ex itaque!</p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="panel panel-default">-->
<!--        <div class="panel-heading">-->
<!--            <button type="button" class="btn btn-default btn-xs spoiler-trigger" data-toggle="collapse">Toggle Button</button>-->
<!--        </div>-->
<!--        <div class="panel-collapse collapse out">-->
<!--            <div class="panel-body">-->
<!--                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque ullam cupiditate repellat dolorem maxime suscipit labore officiis, commodi aliquam fugit in inventore, eum velit. Ex officia placeat veritatis repellat hic.</p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="panel panel-default">-->
<!--        <div class="panel-heading">-->
<!--            <button type="button" class="btn btn-default btn-xs spoiler-trigger" data-toggle="collapse">Toggle Button</button>-->
<!--        </div>-->
<!--        <div class="panel-collapse collapse out">-->
<!--            <div class="panel-body">-->
<!--                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias rerum harum et, earum placeat tempore maiores. Beatae repudiandae aspernatur quae explicabo minus quos tempora illum sed consequuntur? Cumque, est impedit?</p>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!---->
<!---->
<!--<a href="#spoiler-1" data-toggle="collapse" class="btn btn-primary">Открыть</a>-->
<!--<div class="collapse" id="spoiler-1">-->
<!--    <div class="well">-->
<!--        <p>Текст спойлера</p>-->
<!--    </div>-->
<!--</div>-->


