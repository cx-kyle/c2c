<?php

namespace common\models\member;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "rf_member_wallet".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property string $wallet_id 钱包id
 * @property string $wallet_name 钱包昵称
 * @property int $status 状态
 * @property int $create_at 创建时间
 */
class MemberWallet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_wallet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet_name','member_id'], 'trim'],
            [['wallet_name'], 'required'],
            ['member_id', 'unique',  'message' => '这个用户名已经绑定.'],
            ['wallet_name', 'unique', 'targetClass' => '\common\models\member\MemberWallet', 'message' => '这个钱包地址已经被绑定.'],
            [['member_id', 'status', 'created_at'], 'integer'],
            [['wallet_id'], 'string', 'max' => 20],
            [['wallet_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'member_id',
            'wallet_id' => 'wallet_id',
            'wallet_name' => 'wallet_name',
            'status' => '状态',
            'created_at' => '绑定时间',
        ];
    }

    /**
     * 行为插入时间戳
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    //ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }


    /**
     *  绑定用户
     * */
    public function bind()
    {
        $wallet = new MemberWallet();
        $wallet->member_id = Yii::$app->user->identity->member_id;
        $wallet->wallet_name = $this->wallet_name;

        return $wallet->save() ? $wallet : null;
    }

}
