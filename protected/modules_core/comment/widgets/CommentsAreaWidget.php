<?php

/**
 * This widget is used include the comments functionality to a wall entry.
 *
 * @package humhub.modules_core.comment
 * @since 0.5
 */
class CommentsAreaWidget extends HWidget
{

    /**
     * Content Object
     */
    public $object;

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

        $this->render('commentsArea', array(
                'object' => $this->object,
                'id' => $modelName . "_" . $modelId
            )
        );
    }

}

?>
