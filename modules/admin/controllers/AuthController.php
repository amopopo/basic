<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use app\modules\admin\models\Users;

/**
 * Default controller for the `admin` module
 */
class AuthController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    	echo 'login login';
        //return $this->render('index');
    }

    public function actionLogin()
    {
    	echo 'login login';
    	return $this->render('index');
    	//$modelUsers = new \app\models\Users();
    	//$modelUsers = new Users();
    	//exit;
        // return $this->render('login',array(
        // 	'model'=>$modelUsers
        // ));
    }

    public function actionLogout()
    {
    	echo 'logout now';
        //return $this->render('login');
    }
}
