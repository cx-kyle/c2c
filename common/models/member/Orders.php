<?php

namespace common\models\member;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use common\models\member\Pendings;
use yii\web\NotFoundHttpException;
/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property string $pending_id 挂单号
 * @property string $buyer 买方id
 * @property string $seller 卖方id
 * @property string $asset_num 订单资产数量
 * @property string $order_id 订单号
 * @property string $create_at 订单创建时间
 * @property string $update_at 订单更新时间
 * @property string $bindbank_id 支付方式id
 * @property string $status 订单状态
 * @property string $fee 手续费
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pending_id','asset_name'], 'required'],
            [['bindbank_id'], 'integer'],
            [['pending_id', 'buyer','asset_name', 'order_id', 'create_at', 'update_at', 'status', 'fee'], 'string', 'max' => 50],
            [['seller'], 'string', 'max' => 255],
            [['asset_num'], 'string', 'max' => 20],
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
            'buyer' => '买方id',
            'seller' => '卖方id',
            'asset_name' => '资产名称',
            'asset_num' => '资产数量',
            'order_id' => '订单号',
            'create_at' => '订单创建时间',
            'update_at' => '订单更新时间',
            'bindbank_id' => '绑定支付id',
            'status' => '订单状态',
            'fee' => '手续费',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_at','update_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_at'],
                ],
            ],
        ];
    }


    //对应模型文件中
    //查询后减时间戳转化为自定义格式
    public function afterFind()
    {
        //$this->create_at = date('Y-m-d H:i:s',$this->create_at);
        //$this->update_at = date('Y-m-d H:i:s',$this->update_at);
    }

    /**
     *  用户下订单
     * */
    public function bind()
    {

        if (!$this->validate()) {
            return null;
        }

        $tr = Yii::$app->db->beginTransaction();

        try{

            $wallet = new Orders();
            $wallet->pending_id = $this->pending_id;

            // 获取挂单列表
            $models = $this->findModel($wallet->pending_id);

            if($models->type ==1){
                $wallet->buyer = $models->user_id;
                $wallet->seller = Yii::$app->user->identity->member_id;
            }else{
                $wallet->buyer = Yii::$app->user->identity->member_id;
                $wallet->seller = $models->user_id;
            }
            

            //
            $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011].strtoupper(dechex(date('m'))).
            date('d') . substr(time(), -5) .
            substr(microtime(), 2, 5) .
            sprintf('%02d', rand(0, 99));

            $wallet->order_id = $orderSn;

            $wallet->asset_name = $models->asset_name;
            $wallet->asset_num = $this->asset_num;
            $wallet->bindbank_id = $this->bindbank_id;

            $models->asset_num = ($models->asset_num) - ($wallet->asset_num);
            if($models->asset_num >=0){
                $models->save(false);
            }else{
                $tr->rollBack();
                throw new NotFoundHttpException('超出数量');
            }


            $wallet->save();
            $tr->commit();


        }catch (Exception $e){
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return $wallet;

    }


    /**
     * 返回模型
     *
     * @param $id
     * @return mixed
     */
    public function findModel($id)
    {
        if ($model = Pendings::findOne($id))
        {
            return $model;
        }

        throw new \yii\web\BadRequestHttpException('不存在此数据');

    }


    /**
     * 获取挂单用户信息
     *
     */

    public function getUser()
    {
        //同样第一个参数指定关联的子表模型类名
        //
        return $this->hasOne(MemberInfo::className(), ['id' => 'buyer']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPending()
    {
        //同样第一个参数指定关联的子表模型类名
        //
        return $this->hasOne(Pendings::className(), ['id' => 'pending_id']);
    }

    /**
     * 获取绑定支付银行信息
     *
     */
    public function getBank()
    {
        return $this->hasOne(MemberBindbank::className(),['id'=>'bindbank_id']);
    }


}
