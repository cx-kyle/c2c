<?php
/**
 * Created by PhpStorm.
 * User: 24055
 * Date: 2019/1/22
 * Time: 10:26
 */

namespace api\modules\v1\controllers\member;

use Yii;
use common\helpers\ResultDataHelper;
use yii\web\NotFoundHttpException;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\models\member\MemberWallet;
/**
 *  钱包绑定接口
 *
 * */

class WalletController extends OnAuthController
{
    public $modelClass = 'common\models\member\MemberWallet';


    /**
     * 单个显示
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */

    public function actionView($id)
    {
        $model = $this->modelClass::find()
            ->where(['member_id' => $id, 'status' => StatusEnum::ENABLED])
            ->select(['member_id', 'wallet_name', 'created_at', 'status'])
            ->asArray()
            ->one();

        if (!$model)
        {
            throw new NotFoundHttpException('请求的数据不存在或您的权限不足.');
        }

        return $model;
    }


    /**
     *  查看用户下面绑定的支付信息
     *
     */
    public function actionViews()
    {
        $model = $this->modelClass::find()
            ->select(['wallet_name','created_at','status'])
            ->where(['member_id' => Yii::$app->user->identity->member_id,'status' => 1])
            ->asArray()
            ->all();

        if (!$model)
        {
            throw new NotFoundHttpException('请求的数据不存在或您的权限不足.');
        }

        return $model;
    }


    /**
     *  绑定钱包数据
     *
     *
     * */

    public function actionCreate()
    {
        $model = new MemberWallet();

        $model->attributes = Yii::$app->request->post();
        if ($model->validate())
        {
            if ($user = $model->bind()){
                return ['resulte' => '绑定成功！'];
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
        if (in_array($action, ['delete', 'index']))
        {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }



}