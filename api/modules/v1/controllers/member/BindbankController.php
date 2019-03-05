<?php
/**
 * Created by PhpStorm.
 * User: 24055
 * Date: 2019/1/23
 * Time: 9:27
 */
namespace api\modules\v1\controllers\member;

use Yii;
use common\helpers\ResultDataHelper;
use yii\web\NotFoundHttpException;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\models\member\MemberBindbank;
use yii\web\Request;


header('Access-Control-Allow-Origin:*');
/**
 * 用户银行卡绑定
 *
 */

class BindbankController extends OnAuthController
{
    public $modelClass = 'common\models\member\MemberBindbank';

    /**
     * 增加绑定银行卡或者微信以及支付宝
     */

    public function actionCreate()
    {
        $model = new MemberBindbank();
        $model->attributes = Yii::$app->request->post();

        if ($model->validate())
        {
            if ($user = $model->bind()){
                return ['resulte' => '绑定成功'];
            }
        }
        return ResultDataHelper::api(422, $this->analyErr($model->getFirstErrors()));
    }

    /**
     *  查看用户下面绑定的支付信息
     *
     */
    public function actionViews()
    {
        $model = $this->modelClass::find()
            ->where(['member_id' => Yii::$app->user->identity->member_id,'status' => 1])
            ->select(['id','name','account','type','money_photo','bank','branch','status'])
            ->asArray()
            ->all();

        if (!$model)
        {
            throw new NotFoundHttpException('请求的数据不存在或您的权限不足.');
        }

        return $model;
    }



    /**
     *  状态改变
     *  用戶状态
     *
     */
    public function actionState()
    {
        
        /**
         * 修改用戶的狀態，綁定或解綁
         */
        $request = Yii::$app->request;
        if ($request->isPut)  { /* 请求方法是 PUT */
            $param = $request->getBodyParam('id');
        }
        $model = $this->findModel($param);
       
        $model->status = 0;
        if ($model->save())
        {
            header('Access-Control-Allow-Methods:PUT');
            return ['resulte' => '解绑成功'];

        }else {
            throw new NotFoundHttpException('请求的数据不存在或您的权限不足.');
        }


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
        if ($id && ($model = MemberBindbank::findOne($id)))
        {
            $model->status = 0;
            return $model;
        }
        throw new \yii\web\BadRequestHttpException('用户不存在');

    }

}