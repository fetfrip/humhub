<?php
/**
 * This view represents only parent html layers of comment rows and
 * represents new comment form.isLimited is sending true to show
 * just last 2 item.
 *
 * @property Object $object content object.
 *
 * @package humhub.modules_core.comment
 * @since 0.5
 */
?>

<div class="well well-small" style="display: none;" id="comment_<?php echo $id; ?>">
    <div class="comment" id="comments_area_<?php echo $id; ?>">
        <?php $this->widget('application.modules_core.comment.widgets.CommentsWidget', array('object' => $object, 'isLimited' => true, 'shownCommentCount' => Comment::DEFAULT_SHOWN_COMMENT_COUNT)); ?>
    </div>

    <?php $this->widget('application.modules_core.comment.widgets.CommentFormWidget', array('object' => $object)); ?>

</div>
<?php /* END: Comment Create Form */ ?>

<script type="text/javascript">

<?php if (isset($id)) { ?>
        // make comments visible at this point to fixing autoresizing issue for textareas in Firefox
        $('#comment_<?php echo $id; ?>').show();
<?php } ?>

</script>