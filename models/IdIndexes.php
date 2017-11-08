<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "id_indexes".
 *
 * @property integer $id
 * @property string $name
 * @property string $prefix
 * @property integer $start_index
 * @property integer $current_index
 */
class IdIndexes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'id_indexes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['start_index', 'current_index'], 'integer'],
            [['name', 'prefix'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'prefix' => 'Prefix',
            'start_index' => 'Start Index',
            'current_index' => 'Current Index',
        ];
    }

    public function getIndexByName($name){
        $sql = "SELECT * FROM ".$this->tableName()." WHERE name=:name";

        $com = Yii::$app->db->createCommand($sql);
        $com->bindParam(':name',$name);
        $res = $com->queryOne();

        $index = array('index'=>'','current_index'=>0);
        if(!empty($res)){
            $newindex = (($res['current_index']*1)+1);
            $index['index'] = $res['prefix'].str_pad($newindex,$res['min_numbers'],0,STR_PAD_LEFT);
            $index['current_index'] = $newindex;
        }

        return $index;
    }

    public function updateIndexByName($name,$current_index){
        $sql = "update ".$this->tableName()." set current_index=:current_index WHERE name=:name";

        $com = Yii::$app->db->createCommand($sql);
        $com->bindParam(':name',$name);
        $com->bindParam(':current_index',$current_index);
        $res = $com->execute();
    }

}
