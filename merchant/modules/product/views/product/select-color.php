<style>
    .sp-replacer {
        background: #fff;
    }
    .spectrum-group > .input-group-addon {
        padding: 0;
    }
</style>


<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <?= \kartik\color\ColorInput::widget([
                    'name' => 'color_14',
                    'value' => $value,
                    'options' => ['placeholder' => '选择颜色', 'readonly' => true],
                ]);?>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>
