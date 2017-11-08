<?php

namespace app\models;
use yii;
use \yii\db\ActiveRecord;
/*
use yii\base\NotSupportedException;
use yii\helpers\Security;
use yii\web\IdentityInterface;*/


/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $title
 * @property string $name
 * @property string $userid
 * @property integer $mobile_countrycode
 * @property integer $mobile_number
 * @property string $email
 * @property string $password
 * @property integer $status
 * @property string $usertype
 */
class Users extends ActiveRecord
// class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{users}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile_countrycode', 'mobile_number', 'password'], 'required'],
            [['mobile_countrycode', 'mobile_number', 'status'], 'integer'],
            [['title', 'usertype'], 'string', 'max' => 10],
            [['userid'], 'string', 'max' => 45],
            [['name','email','password'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['userid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'name' => 'Name',
            'userid' => 'User id',
            'mobile_countrycode' => 'Mobile country code',
            'mobile_number' => 'Handphone number',
            'email' => 'Email',
            'password' => 'Password',
            'status' => 'Status',
            'usertype' => 'Usertype',
        ];
    }

    public function getUserByMobileNumber($mobile_countrycode,$mobile_number,$status='active'){
        $sql = "SELECT * FROM ".$this->tableName()." WHERE mobile_countrycode=:mobile_countrycode AND mobile_number=:mobile_number ";
        if($status!='active'){
            $sql .= " AND status=:status ";
        }else{
            $sql .= " AND status!=0 "; //deleted
        }
        $sql .= " order by id desc";

        $com = Yii::$app->db->createCommand($sql);
        $com->bindParam(':mobile_countrycode',$mobile_countrycode);
        $com->bindParam(':mobile_number',$mobile_number);
        if($status!='active'){
            $com->bindParam(':status',$status);
        }
        $res = $com->queryOne();

        return $res;
    }
}

