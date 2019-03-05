<?php

namespace common\models\member;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "{{%member_privacy}}".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property string $surname 姓
 * @property string $realname 名字
 * @property string $datebirth 出生年月
 * @property string $country 国籍
 * @property int $sex 性别 
 * @property string $profession 职业
 * @property string $certtype 证件类型
 * @property string $certnum 证件号码
 * @property string $frontphoto 身份证正面照
 * @property string $backphoto 身份证背面照
 * @property string $handphoto 手持身份证
 * @property int $status 状态
 * @property int $reason 状态
 * 
 */
class MemberPrivacy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_privacy}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname', 'realname', 'datebirth', 'sex',  'certtype', 'certnum'], 'required'],
            [['id', 'member_id', 'sex','certtype', 'status'], 'integer'],
            [['surname', 'realname', 'country'], 'string', 'max' => 20],
            [['profession','certnum','reason'], 'string', 'max' => 50],
            [['frontphoto', 'backphoto', 'handphoto'], 'string', 'max' => 255],
            [['id'], 'unique'],
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
            'surname' => '姓',
            'realname' => '名字',
            'datebirth' => '出生年月',
            'country' => '国籍',
            'sex' => '性别 0:男,1:女 ',
            'profession' => '职业',
            'certtype' => '证件类型 0:身份证,1:护照',
            'certnum' => '证件号码',
            'frontphoto' => '身份证正面照',
            'backphoto' => '身份证背面照',
            'handphoto' => '手持身份证',
            'status' => '状态:0:填写资料,1:上传图片,2:提交审核,3:审核通过,4:认证失败',
            'reason' => '审核内容',
        ];
    }


    /**
     *  绑定用户身份信息
     * */
    public function bind()
    {

        if (!$this->validate()) {
            return null;
        }
        $prinfo = new MemberPrivacy();
        $prinfo->member_id = Yii::$app->user->identity->member_id;
        $prinfo->surname = $this->surname;
        $prinfo->realname = $this->realname;
        $prinfo->datebirth = $this->datebirth;
        $prinfo->sex = $this->sex;
        $prinfo->certtype = $this->certtype;
        $prinfo->certnum = $this->certnum;
        $prinfo->frontphoto = $this->frontphoto;
        $prinfo->backphoto = $this->backphoto;
        $prinfo->handphoto = $this->handphoto;

        return $prinfo->save() ? $prinfo : null;
    }


    /**
     * 修改用户身份信息
     * 
     */

     public function edit(){
         
     }


}
