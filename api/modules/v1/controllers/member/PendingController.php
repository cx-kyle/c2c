<?php
/**
 * Created by PhpStorm.
 * User: 24055
 * Date: 2019/2/14
 * Time: 9:27
 */
namespace api\modules\v1\controllers\member;

use yii;
use common\helpers\ResultDataHelper;
use yii\web\NotFoundHttpException;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\models\member\Pendings;
use common\models\member\MemberBindbank;
/**
 * 挂单接口
 *
 * Class InfoController
 * @package api\modules\v1\controllers\member
 * @property \yii\db\ActiveRecord $modelClass
 * @author cx
 */

class PendingController extends OnAuthController
{

    public $modelClass = 'common\models\member\Pendings';

    /**
 * 不用进行登录验证的方法
 * 例如： ['index', 'update', 'create', 'view', 'delete']
 * 默认全部需要验证
 *
 * @var array
 */
    protected $optional = ['index','view'];


    /**
     * 显示所有挂单列表
     * 
     */
    public function actionList($type)
    {
        $request = Yii::$app->request->get();

        $data = Pendings::find()
            ->select(['rf_pendings.*','rf_member_info.username'])
            ->where(['rf_pendings.type'=>$type])
            ->joinWith('userinfo',false)
            ->joinWith('bankinfo')
            ->asArray()
            ->all();

        // $dataProvider = MemberBindbank::find()
        //     ->select(['type'])   
        //     ->where(['member_id' =>Yii::$app->user->identity->member_id ]) 
        //     ->asArray()
        //     ->all();
        // $return = [$data,$dataProvider];

        return $data;

    }


    /**
     * 显示个人挂单列表
     *
     */
    
    public function actionViews()
    {

        $model = $this->modelClass::find()
            ->where(['user_id' => Yii::$app->user->identity->member_id])
            ->asArray()
            ->all();
        return $model;

    }

    /**
     *  增加挂单
     *
     */

    public function actionCreate()
    {
        $model = new Pendings();
        $model->attributes = Yii::$app->request->post();

        if ($model->validate())
        {
            if ($user = $model->bind()){
                return ['resulte' => '挂单成功'];
            }
        }
        return ResultDataHelper::api(422, $this->analyErr($model->getFirstErrors()));
    }


    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['delete']))
        {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }


    /**
     * 返回模型
     *
     * @param $id
     * @return mixed
     */
    protected function findModel($id)
    {
        if ($id && ($model = MemberPrivacy::findOne(['user_id' => $id])))
        {
            return $model;
        }
        throw new \yii\web\BadRequestHttpException('不存在此数据');

    }

}