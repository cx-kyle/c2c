<?php

namespace common\models\member;

use Yii;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "{{%member_bindbank}}".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property int $type 收款方式：1:支付宝,2:微信,3:银行卡
 * @property string $name 姓名
 * @property string $account 收款账号
 * @property string $money_photo 付款码
 * @property string $bank 开户行
 * @property string $branch 开户支行
 * @property int $status 状态
 */
class MemberBindbank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_bindbank}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name', 'account'], 'required'],
            [['member_id', 'type', 'status'], 'integer'],
            [['name', 'bank'], 'string', 'max' => 20],
            [['account','branch'], 'string', 'max' => 50],
            [['money_photo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'type' => '收款方式：0:支付宝,1:微信,2:银行卡',
            'name' => '姓名',
            'account' => '收款账号',
            'money_photo' => '付款码',
            'bank' => '开户行',
            'branch' => '开户支行',
            'status' => '状态',
        ];
    }

    /**
     *  绑定银行信息，支付宝，微信
     *
     */
    public function bind()
    {
        if (!$this->validate()) {
            return null;
        }
        $prinfo = new MemberBindbank();
        $prinfo->member_id = Yii::$app->user->identity->member_id;
        $prinfo->type = $this->type;
        $prinfo->name = $this->name;
        $prinfo->account = $this->account;

        if($prinfo->type == 0 || $prinfo->type == 1 ){
            /**
             * 支付宝,微信信息绑定;
             */

            if (!$this->money_photo)
            {
                throw new NotFoundHttpException('付款码不能为空.');
            }
            $prinfo->money_photo = $this->money_photo;

        }else{
            /**
             * 银行卡信息;
             */
            if (!$this->bank)
            {
                throw new NotFoundHttpException('开户行不能为空.');
            }

            $prinfo->bank = $this->bank;   // 开户行
            $prinfo->branch = $this->branch; // 开户支行
        }


        return $prinfo->save() ? $prinfo : null;
    }




}
