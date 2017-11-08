<?php

namespace app\modules\admin;

use Yii;
/**
 * admin module definition class
 */
class adminModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::configure($this, require __DIR__ . '/config/config.php');

       // Yii::$app->user->setStateKeyPrefix('_backend');
        // Yii::$app->set('session', [
        //     'class' => 'yii\web\Session',
        //     'name' => '_adminSessionId',
        // ]);

        Yii::$app->set('backend', [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\admin\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['admin/auth/login'],
        ]);


       // $this->layoutPath = \Yii::getAlias('@app/themes/admin/views/layouts');
        $this->layout = 'main';
        /*$this->layout = '@app/modules/admin/views/layouts/main';*/
        // $this->theme = new \yii\base\Theme([
        //     'pathMap' => ['@app/views' => '@app/themes/admin/views'],
        //     'baseUrl' => '@app/themes/admin',
        // ]);
    }

    public function beforeAction($action) {

       // $this->layout = "@app/modules/admin/views/layouts/main.php";

        if (parent::beforeAction( $action)) {
            $route = Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/'.$action->id;
            $publicPages = array(
                'admin/auth/login'
            );

            if (Yii::$app->backend->isGuest && !in_array($route, $publicPages) ){            
              // Yii::$app->backend->loginRequired();
            }
            else
                return true;
        }
        else
            return false;
    }
}
