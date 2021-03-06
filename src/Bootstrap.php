<?php

namespace DevGroup\DeferredTasks;

use DevGroup\DeferredTasks\commands\DeferredController;
use DevGroup\DeferredTasks\events\DeferredQueueEvent;
use DevGroup\DeferredTasks\handlers\QueueCompleteEventHandler;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;

class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {

            // add deferred module
            $app->setModule('deferred', new DeferredTasksModule('deferred', $app));

            // this will automatically add deferred controller to console app
            $app->controllerMap['deferred'] = [
                'class' => DeferredController::className(),
            ];

        }
        $app->i18n->translations['deferred-tasks'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => __DIR__ . DIRECTORY_SEPARATOR . 'messages',
        ];

        DeferredQueueEvent::on(
            DeferredController::className(),
            DeferredController::EVENT_DEFERRED_QUEUE_COMPLETE,
            [QueueCompleteEventHandler::className(), 'handleEvent']
        );
    }
}