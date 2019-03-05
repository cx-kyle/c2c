<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\models\api\AccessToken;
use common\helpers\ResultDataHelper;
use api\controllers\OnAuthController;
use api\modules\v1\models\LoginForm;
use api\modules\v1\models\RefreshForm;
use api\modules\v1\models\ApiSignupForm;
use common\models\member\MemberInfo;
use common\models\member\MemberBindbank;
use common\models\member\Orders;
/**
 * 登录接口
 *
 * Class SiteController
 * @package api\modules\v1\controllers
 * @author cx
 */
class SiteController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $optional = ['login', 'refresh','signup'];

    /**
     * 登录根据用户信息返回accessToken
     *
     * @return array|bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();
        if ($model->validate())
        {
            return AccessToken::getAccessToken($model->getUser(), $model->group);
        }

        // 返回数据验证失败
        return ResultDataHelper::api(422, $this->analyErr($model->getFirstErrors()));
    }


    /**
     * @return array|mixed
     */
    public function actionInfo($user_id){
        $model = MemberInfo::find()
            ->select(['username','created_at as create_at'])
            ->where(['id'=>$user_id])
            ->asArray()
            ->one();
        $dataprovice = MemberBindbank::find()
            ->select(['id','name','type','account','money_photo','bank'])
            ->where(['member_id'=>$user_id,'status'=>1])
            ->asArray()
            ->all();
            $province = Orders::find()
            ->select(['count(*) as quantity',])
            ->where(['or',['buyer' => $user_id],['seller' =>$user_id]])
            ->asArray()
            ->all();
        $percentage = Orders::find()
            ->select(['count(*) as volume'])
            ->where(['or',['buyer' => $user_id],['seller' =>$user_id]])
            ->andWhere(['status'=>4])
            ->asArray()
            ->all();
        $model['bankinfo'] = $dataprovice;
        if(empty($province) ){
            
            $model['quantity'] = 0;
        }
        else{
            $model['quantity'] = $province[0]['quantity'];
        }
        if(empty($percentage)  ){
            $model['volume'] = 0;
        }else{
            $model['volume'] = $percentage[0]['volume'];
            
        }

        
        if(empty($percentage) && empty($province) ){
            
            $model['percentage'] = "0%";
        }else{
            $model['percentage'] = round(( $model['volume']/$model['quantity'])*100,2)."%";
        }
        
        return $model;
    }


    /**
     *
     *  注册 用户成功信息返回accessToken
     *
     */

    public function actionSignup()
    {
        $model = new ApiSignupForm();

        $model->attributes = Yii::$app->request->post();
        if ($model->validate())
        {
            if ($user = $model->signup()){
                return ['resulte' => '注册成功！'];
            }

        }

        return ResultDataHelper::api(422, $this->analyErr($model->getFirstErrors()));
    }

    /**
     * 重置令牌
     *
     * @param $refresh_token
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionRefresh()
    {
        $model = new RefreshForm();
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate())
        {
            return ResultDataHelper::api(422, $this->analyErr($model->getFirstErrors()));
        }

        return AccessToken::getAccessToken($model->getUser(), $model->group);
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
        if (in_array($action, ['index', 'view', 'update', 'create', 'delete']))
        {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}
