<?php
namespace app\components;
 
 
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
 
class Monstar extends Component
{
	const API_SECRETKEY = '*^34_-+=mon@65908';


	public function get_api_secretkey()
	{
		return self::API_SECRETKEY;
	}
}

?>