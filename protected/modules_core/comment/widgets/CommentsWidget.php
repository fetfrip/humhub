<?php

/**
 * Renders comments without parent html layers. It shows a excerpt of all comments,
 * but provides the functionality to show all comments.
 *
 * @package humhub.modules_core.comment
 * @since 0.5
 */
class CommentsWidget extends HWidget
{

    /**
     * Content Object
     */
    public $object;

    /**
     * Are all comments visible or not?
     */
    public $isLimited;

    /*
     * Shown comment count at a time.
     */
    public $shownCommentCount;

    public function init()
    {
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->assetManager->publish(
                        Yii::getPathOfAlias('application.modules_core.comment.resources') . '/ws.js'
                ), CClientScript::POS_BEGIN
        );
    }

    /**
     * Executes the widget.
     */
    public function run()
    {
        $modelName = $this->object->content->object_model;
        $modelId = $this->object->content->object_id;
        $shownCommentCount = $this->shownCommentCount;

        // Count all Comments. Get this count above of the isLimited flag since
        // it takes into account comment count if the content just created.
        $commentCount = Comment::GetCommentCount($modelName, $modelId);

        $isLimited = ($commentCount < Comment::DEFAULT_SHOWN_COMMENT_COUNT) ? false : $this->isLimited;

        // Shown comment count value is writing to the redis server.
        $shownCommentCountCacheId = 'shown_comment_count_' . $modelName . '_' . $modelId;
        Yii::app()->redis->getClient()->set($shownCommentCountCacheId, $shownCommentCount, Comment::CACHE_TIMEOUT);

        // Putting the visibility of the old comments to the redis in order to determine to show after delete
        // and post contents.
        $isLimitedCacheId = 'is_limited_'.$modelName.'_'.$modelId;
        Yii::app()->redis->getClient()->set($isLimitedCacheId, $isLimited, Comment::CACHE_TIMEOUT);

        // Deleting caches since the following GetCommentsLimited method gets the comments from cache always.
        // And because of that we can not show and hide old comments.
        Yii::app()->cache->delete(sprintf("commentCount_%s_%s", $modelName, $modelId));
        Yii::app()->cache->delete(sprintf("commentsLimited_%s_%s", $modelName, $modelId));

        $comments = Comment::GetCommentsLimited($modelName, $modelId, ($isLimited ? $shownCommentCount : $commentCount));

        $this->render('comments', array(
                'comments' => $comments,
                'commentCount' => $commentCount,
                'modelName' => $modelName,
                'modelId' => $modelId,
                'id' => $modelName . "_" . $modelId,
                'isLimited' => $isLimited
            )
        );
    }

}

?>
