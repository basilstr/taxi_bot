<?php
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

?>
<div class="section-body contain-lg">
    <!-- BEGIN INTRO -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="text-primary">Аккаунт</h1>
        </div>
        <!--end .col -->
        <div class="col-lg-8">
            <article class="margin-bottom-xxl">
                <p class="lead">
                    При зміні облікових даних необхідно бути уважним. Дані зміни можуть впливати на подальшу роботу
                    ваших систем та інформування.
                </p>
            </article>
        </div>
        <!--end .col -->
    </div>
    <!--end .row -->
    <!-- END INTRO -->
    <!-- BEGIN BASIC ELEMENTS -->
    <div class="row">
        <div class="col-lg-offset-1 col-md-4 col-sm-6">
            <div class="card">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'account-form',
                    'options' => ['class' => 'form'],
                ]);
                ?>
                <div class="card-body">
                <?= $form->field($model, 'avatar')->widget(FileInput::class, [
                    'options'=>['multiple' => false, 'accept'=>'image/*'],
                    'pluginOptions' => [
                        'showPreview' => true,
                        'showCaption' => false,
                        'showRemove' => false,
                        'showUpload' => false,
                        'showCancel' => false,
                        'initialPreviewShowDelete' => false,
                        'allowedFileExtensions' => ['jpg', 'gif', 'jpeg', 'png',],
                        'previewFileType' => 'any',
                        'initialPreviewAsData' => false,
                        'msgUploadBegin' => Yii::t('app', 'Please wait, system is uploading the files'),
                        'msgUploadThreshold' => Yii::t('app', 'Please wait, system is uploading the files'),
                        'msgUploadEnd' => 'Завантажено',
                        'dropZoneClickTitle' => '',
                        "uploadAsync" => false,
                        "browseOnZoneClick" => true,
                        'fileActionSettings' => [
                            'width' => '120px',
                            'showCaption' => false,
                            'showZoom' => false,
                            'showRemove' => false,
                            'showUpload' => true,
                        ],
                        'maxFileCount' => 50, 'minFileSize' => 0, 'maxFileSize' => 10000000, // 100 мб
                        'msgPlaceholder' => '',
                        'resizeImages' => false,
                        'browseLabel' => 'Вибрати файл', // название для кнопки
                        'initialPreview'=>[
                            "/uploads/avatars/".$model->avatar,
                        ],
                        'initialPreviewAsData'=>true,
                        'layoutTemplates' => [
                            'footer' => '
                        <div class="file-thumbnail-footer">
                        <div class="file-caption-name"></div>
                        </div>
                    ',
                        ],
                    ],
                    'pluginEvents' => [
                        'filebatchselected' => 'function() {
                            $("#img_avatar").attr("src","");
                        }',
                        'fileloaded' => 'function(event, file, previewId, fileId, index, reader) {
                            $("#img_avatar").attr("src","");
                        }',
                        // filebatchuploadsuccess - метод сработает после полной загрузки ajax
                        'filebatchuploadsuccess' => 'function(event, data) {
                       console.log(data);
                        }',
                    ],
                    ]);
                ?>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'name') ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'password_repeat')->passwordInput() ?>
                    <?= $form->field($model, 'phone') ?>
                    <?= $form->field($model, 'email') ?>
                    <button type="submit" class="btn btn-primary ink-reaction">Змінити</button>
                </div>
                <!--end .card-body -->
                <?php ActiveForm::end() ?>
            </div>
            <!--end .card -->
        </div>
        <!--end .col -->
    </div>
    <!--end .row -->
</div><!--end .section-body -->

