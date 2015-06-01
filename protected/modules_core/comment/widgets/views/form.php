<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>


<?php /* BEGIN: Comment Create Form */ ?>
<div id="comment_create_form_<?php echo $id; ?>" class="comment_create">

    <?php echo CHtml::form("#"); ?>
    <?php echo CHtml::hiddenField('model', $modelName); ?>
    <?php echo CHtml::hiddenField('id', $modelId); ?>

    <?php echo CHtml::textArea("message", "", array('id' => 'newCommentForm_' . $id, 'rows' => '1', 'class' => 'form-control autosize commentForm', 'placeholder' => CHtml::encode(Yii::t('CommentModule.widgets_views_form', 'Write a new comment...')))); ?>

    <?php
    $this->widget('application.widgets.HEditorWidget', array(
        'id' => 'newCommentForm_' . $id,
    ));
    ?>

    <?php
    // Creates Uploading Button
    $this->widget('application.modules_core.file.widgets.FileUploadButtonWidget', array(
        'uploaderId' => 'comment_upload_' . $id,
        'fileListFieldName' => 'fileList',
    ));
    ?>

    <?php
    echo HHtml::ajaxSubmitButton(Yii::t('CommentModule.widgets_views_form', 'Post'), CHtml::normalizeUrl(array('/comment/comment/post')), array(
            'type' => 'POST',
            'beforeSend' => "function() {
                $('#comment_create_post_" . $id . "').prop('disabled', true);
            }",
            'success' => "function(html) {
                $('#comment_create_post_" . $id . "').prop('disabled', false);
                $('#comments_area_" . $id . "').html(html);
                $('#newCommentForm_" . $id . "').val('').trigger('autosize.resize');
                var contentEditableElement = $('#newCommentForm_" . $id . "_contenteditable');
                contentEditableElement.html('" . CHtml::encode(Yii::t('CommentModule.widgets_views_form', 'Write a new comment...')) . "');
                contentEditableElement.addClass('atwho-placeholder');
                resetUploader('comment_upload_" . $id . "');
            }",
        ), array(
            'id' => "comment_create_post_" . $id,
            'class' => 'btn btn-small btn-primary',
            'style' => 'position: absolute; left: -90000000px; opacity: 0;',
        )
    );
    ?>

    <?php echo Chtml::endForm(); ?>


    <?php
    // Creates a list of already uploaded Files
    $this->widget('application.modules_core.file.widgets.FileUploadListWidget', array(
        'uploaderId' => 'comment_upload_' . $id,
    ));
    ?>
</div>

<script>

    $(document).ready(function () {

        // add attribute to manage the enter/submit event (prevent submit, if user press enter to insert an item from atwho plugin)
        $('#newCommentForm_<?php echo $id; ?>_contenteditable').attr('data-submit', 'true');

        // Fire click event for comment button by typing enter
        $('#newCommentForm_<?php echo $id; ?>_contenteditable').keydown(function (event) {
            // by pressing enter without shift
            if (event.keyCode == 13 && event.shiftKey == false) {

                // prevent default behavior
                event.cancelBubble = true;
                event.returnValue = false;
                event.preventDefault();


                // check if a submit is allowed
                if ($('#newCommentForm_<?php echo $id; ?>_contenteditable').attr('data-submit') == 'true') {

                    // get plain input text from contenteditable DIV
                    $('#newCommentForm_<?php echo $id; ?>').val(getPlainInput($('#newCommentForm_<?php echo $id; ?>_contenteditable').clone()));

                    // set focus to submit button
                    $('#comment_create_post_<?php echo $id; ?>').focus();

                    // emulate the click event
                    $('#comment_create_post_<?php echo $id; ?>').click();
                }
            }

            return event.returnValue;
        });

        // This workaround creates a temporary input element and focus it in order to avoid unwanted focus problem after submit a comment.
        $('#newCommentForm_<?php echo $id; ?>_contenteditable').on('blur', function() {
            var editableFix = jQuery('<input style="width:1px;height:1px;border:none;margin:0;padding:0;" tabIndex="-1">').appendTo($(this));
            editableFix.focus();
            editableFix[0].setSelectionRange(0, 0);
            editableFix.blur();
            editableFix.remove();
        });

        // set the size for one row (Firefox)
        $('#newCommentForm_<?php echo $id; ?>').css({height: '36px'});

        // add autosize function to input
        $('.autosize').autosize();


        $('#newCommentForm_<?php echo $id; ?>_contenteditable').on("shown.atwho", function (event, flag, query) {
            // prevent the submit event, by changing the attribute
            $('#newCommentForm_<?php echo $id; ?>_contenteditable').attr('data-submit', 'false');
        });

        $('#newCommentForm_<?php echo $id; ?>_contenteditable').on("hidden.atwho", function (event, flag, query) {

            var interval = setInterval(changeSubmitState, 10);

            // allow the submit event, by changing the attribute (with delay, to prevent the first enter event for insert an item from atwho plugin)
            function changeSubmitState() {
                $('#newCommentForm_<?php echo $id; ?>_contenteditable').attr('data-submit', 'true');
                clearInterval(interval);
            }
        });

    });

</script>