<?php

namespace common\models\member;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

use common\models\member\MemberInfo;

/**
 * This is the model class for table "{{%pendings}}".
 *
 * @property int $id
 * @property string $pending_id 挂单号
 * @property string $user_id 挂单用户id
 * @property string $type 挂单类型
 * @property string $asset_name 币种名称
 * @property string $asset_price 币种单价
 * @property string $asset_num 币种数量
 * @property string $money_num 币种金额
 * @property int $status 挂单状态
 * @property string $create_at 创建时间
 * @property string $update_at 更新时间
 */
class Pendings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%pendings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'asset_name', 'asset_price', 'asset_num'], 'required'],
            [['status'], 'integer'],
            ['pending_id', 'unique', 'targetClass' => '\common\models\member\Pendings', 'message' => '挂单号已存在'],
            [['pending_id'], 'string', 'max' => 50],
            [['user_id', 'asset_name'], 'string', 'max' => 20],
            [['create_at', 'update_at'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pending_id' => '挂单号',
            'user_id' => '挂单用户id',
            'type' => '挂单类型',
            'asset_name' => '币种名称',
            'asset_price' => '币种单价',
            'asset_num' => '币种数量',
            'money_num' => '币种金额',
            'status' => '挂单状态',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];
    }

    /**
     *  用户挂单
     * */
    public function bind()
    {

        if (!$this->validate()) {
            return null;
        }
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011].strtoupper(dechex(date('m'))).
            date('d') . substr(time(), -5) .
            substr(microtime(), 2, 5) .
            sprintf('%02d', rand(0, 99));
        $wallet = new Pendings();
        $wallet->pending_id = $orderSn;
        $wallet->user_id = Yii::$app->user->identity->member_id;
        $wallet->type = $this->type;
        $wallet->asset_name = $this->asset_name;
        $wallet->asset_price = $this->asset_price;
        $wallet->asset_num = $this->asset_num;
        $wallet->money_num = $this->money_num;

        return $wallet->save() ? $wallet : null;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_at','update_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_at'],
                ],
            ],
        ];
    }


    /**
     * 获取挂单用户信息
     * 
     */

    public function getUserinfo()
    {
        //同样第一个参数指定关联的子表模型类名
        //
        return $this->hasOne(MemberInfo::className(), ['id' => 'user_id']);
    }

    /**
     * 获取用户认证信息
     *
     */
    public function getBankinfo()
    {
        //同样第一个参数指定关联的子表模型类名
        //
        return $this->hasMany(MemberBindbank::className(), ['member_id' => 'user_id'])
            ->onCondition([MemberBindbank::tableName().'.status'=>1]);;
    }

}
