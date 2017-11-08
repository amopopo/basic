<?php

namespace app\modules\api\controllers;

use yii\web\Controller;
use yii;

// use app\models;
use app\components\Monstar;

/**
 * Default controller for the `api` module
 */
class UserController extends Controller
{
	public function beforeAction($action) {
	    $this->enableCsrfValidation = false;
	    return parent::beforeAction($action);
	}

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        //return $this->render('index');
    }

    public function actionRegister(){
    	$response = array(
    			'status'=>'',
    			'msg'=>'',
    			'data'=>array()
    		);

    	$request = Yii::$app->request;
    	$_key = $request->post('_key');
    	if (!$request->isPost || empty($_key)) { 
    		$response = $this->errormsg('Invalid api call'); 
    	}else{
    		$errors = array();

	    	$name = $request->post('name');
	    	$mobile = $request->post('mobile_number');
	    	$password = base64_decode($request->post('password'));

	    	$mobile_countrycode = '65'; //default, keep for future use

	    	if(empty($mobile)){
	    		$errors[] = 'Please enter your handphone number.';
	    	}

	    	if(empty($password)){
	    		$errors[] = 'Please enter your password.';
	    	}else{
	    		if(strlen($password) > 45){
	    			$errors[] = 'Password should contain at most 45 characters.';
	    		}
	    	}

	    	if(empty($errors)){
	    		$api_secretkey = Monstar::get_api_secretkey();
	    		$serverside_key = base64_encode($password.$mobile.$name.$api_secretkey);
				// echo $serverside_key;
	    		if($_key != $serverside_key){
	    			$errors[] = 'Invalid key';
	    		}
	    	}

	    	if(!empty($errors)){
	    		$response = $this->errormsg('Invalid data',$errors);
	    	}else{
	    		//start of the rest of the validation
	    		$modelUsers = new \app\models\Users();
	    		$modelIdIndexes = new \app\models\IdIndexes();
	    		$userid = $modelIdIndexes->getIndexByName('user');
	    		$modelUsers->userid = $userid['index'];
	    		
	    		$modelUsers->mobile_countrycode = $mobile_countrycode;

	    		$postvalue = $request->post();
	    		$postvalue['password'] = $password;
	    		$modelUsers->attributes = $postvalue;

	    		if ($modelUsers->validate()) {
	    			//check if mobile country code + mobile number is unique
	    			$existinguser = $modelUsers->getUserByMobileNumber($mobile_countrycode,$mobile,1);
	    			if(empty($existinguser)){
	    				// all inputs are valid
					    $modelUsers->password = password_hash($password, PASSWORD_DEFAULT);
					    if(!$modelUsers->save()){
					    	$errors = $modelUsers->errors;
				    		$response = $this->errormsg('Invalid data',$errors);
					    }else{
					    	if(!empty($userid)){
					    		$modelIdIndexes->updateIndexByName('user',$userid['current_index']);
					    	}
					    	
					    	$response['status'] = 'success';
						    $response['msg'] = 'User created successfully.';
						    $response['data']['userid'] = $userid['index'];
					    }
	    			}else{
	    				$errors[] = 'Handphone number is not unique.';
	    				$response = $this->errormsg('Invalid data',$errors);
	    			}
				} else {
				    // validation failed: $errors is an array containing error messages
				    $errors = $modelUsers->errors;
				    $response = $this->errormsg('Invalid data',$errors);
				}
	    	}
    	}

    	echo json_encode($response);
    	exit;
    }

    public function actionLogin(){
    	$response = array(
    			'status'=>'',
    			'msg'=>'',
    			'data'=>array()
    		);

    	$request = Yii::$app->request;
    	$_key = $request->post('_key');
    	if (!$request->isPost || empty($_key)) { 
    		$response = $this->errormsg('Invalid api call'); 
    	}else{
    		$mobile = $request->post('mobile_number');
	    	$password = base64_decode($request->post('password'));

    		$errors = array();
	    	if(empty($mobile)){
	    		$errors[] = 'Please enter your handphone number.';
	    	}

	    	if(empty($password)){
	    		$errors[] = 'Please enter your password.';
	    	}

	    	if(empty($errors)){
	    		$api_secretkey = Monstar::get_api_secretkey();
	    		$serverside_key = base64_encode($password.$mobile.$api_secretkey);
				//echo $serverside_key;
	    		if($_key != $serverside_key){
	    			$errors[] = 'Invalid key';
	    		}
	    	}

	    	if(!empty($errors)){
	    		$response = $this->errormsg('Invalid data',$errors);
	    	}else{
	    		//start of the rest of the validation
	    		$modelUsers = new \app\models\Users();
	    		
    			//check if mobile country code + mobile number is unique
    			$existinguser = $modelUsers->getUserByMobileNumber('65',$mobile);
    			if(empty($existinguser)){
    				$errors[] = 'Invalid handphone number or password';
    				$response = $this->errormsg('Invalid data',$errors);
    			}else{
    				if($existinguser['status']==2){
    					$errors[] = 'You account has been blocked. Please contact administrator for more information.';
    					$response = $this->errormsg('Invalid data',$errors);
    				}else if($existinguser['status']==3){
    					$errors[] = 'You account has been deactivated. Please contact administrator for more information.';
    					$response = $this->errormsg('Invalid data',$errors);
    				}else{
    					if (password_verify($password, $existinguser['password'])) {
    						$userinfo = $existinguser;
    						unset($userinfo['password']);
						    $response = $this->successmsg('Valid login credentials',$userinfo);

						    //update last login time?
						}else{
							$errors[] = 'Invalid handphone number or password';
    						$response = $this->errormsg('Invalid data',$errors);

    						//blocked user when exceeded x time wrong login?
						}
    				}
    			}

    			if(!empty($errors)){
    				$response = $this->errormsg('Invalid data',$errors);	
    			}
	    	}
    	}

    	echo json_encode($response);
    	exit;
    }

    private function errormsg($errormsg,$data=array()){
    	if(!empty($data)){
    		$data = array('errors'=>$data);
    	}
    	return array(
    			'status'=>'fail',
    			'msg'=>$errormsg,
    			'data'=>$data
    		);	
    }

    private function successmsg($msg,$data=array()){
    	return array(
    			'status'=>'success',
    			'msg'=>$msg,
    			'data'=>$data
    		);	
    }
}