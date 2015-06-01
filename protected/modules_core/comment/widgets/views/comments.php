<?php
/**
 * This view represents the initial view of comments inside the wall.
 * Inital means, that not all comments are display, just the last 2.
 *
 * @property Array $comments a list of comments to display
 * @property String $modelName The Model (e.g. Post) which the comments belongs to
 * @property Int $modelId The Primary Key of the Model which the comments belongs to
 * @property Int $commentCount the number of total existing comments for this object
 * @property Boolean $isLimited indicates if not all comments are shown
 * @property String $id is a unique Id on Model and PK e.g. (Post_1)
 *
 * @package humhub.modules_core.comment
 * @since 0.5
 */
?>

<?php if ($commentCount > 2) { ?>

    <?php
    // Create an ajax link, which loads all comments upon request
    $showAllLabel = Yii::t('CommentModule.widgets_views_comments', $isLimited ? 'Show all {total} comments.' : 'Hide old comments.', array('{total}' => $commentCount));
    $reloadUrl = CHtml::normalizeUrl(Yii::app()->createUrl('comment/comment/show', array('model' => $modelName, 'id' => $modelId, 'isLimited' => !$isLimited)));
    echo HHtml::ajaxLink($showAllLabel, $reloadUrl, array(
        'beforeSend' => "function(){ $('#comments_loader_" . $id . "').show(); }",
        'complete' => "function(){ $('#comments_loader_" . $id . "').hide(); }",
        'success' => "function(html) { $('#comments_area_" . $id . "').html(html); }",
    ), array('id' => $id . "_showAllLink", 'class' => 'show show-all-link'));
    ?>

    <hr>

    <div id="comments_loader_<?php echo $id; ?>" class="loader comments-loader"></div>

<?php } ?>

<?php foreach ($comments as $comment) : ?>
    <?php $this->widget('application.modules_core.comment.widgets.ShowCommentWidget', array('comment' => $comment)); ?>
<?php endforeach; ?>
