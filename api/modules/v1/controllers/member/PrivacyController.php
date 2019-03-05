<?php
/**
 * Created by PhpStorm.
 * User: 24055
 * Date: 2019/1/22
 * Time: 14:30
 */
namespace api\modules\v1\controllers\member;

use Yii;
use common\helpers\ResultDataHelper;
use yii\web\NotFoundHttpException;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\models\member\MemberPrivacy;

header('Access-Control-Allow-Origin:*');
/**
 * 用户身份认证接口
 *
 *
 */
class PrivacyController extends OnAuthController
{

    public $modelClass = 'common\models\member\MemberPrivacy';

    /**
     *  绑定用户身份数据
     *
     * */
    public function actionCreate()
    {

        $request = Yii::$app->request->post();
        $model = $this->findModels(Yii::$app->user->identity->member_id); 

        if(isset( $request['status'])){
            $model->status = $request['status'];
            $model->save();
            return ['resulte' => '修改成功'];
        }
        


        // $model = new MemberPrivacy();
        // $model->attributes = Yii::$app->request->post();
 

        // if ($model->validate())
        // {
        //     if ($user = $model->bind()){
        //         return ['resulte' => '下一步'];
        //     }
        // }
        // return ResultDataHelper::api(422, $this->analyErr($model->getFirstErrors()));

        
    }


    /**
     * 用户绑定身份证照片
     * 
     */

     public function actionBindphoto()
     {
        $request = Yii::$app->request->post();
        $model = $this->findModel(Yii::$app->user->identity->member_id); 

        if(isset( $request['frontphoto']) && isset($request['backphoto'])  &&  isset( $request['handphoto'])){

            $model->frontphoto = $request['frontphoto'];
            $model->backphoto = $request['backphoto'];
            $model->handphoto = $request['handphoto'];
            $model->status = 2;
        }else{
            throw new NotFoundHttpException('缺少参数');
        }
        

        if ($model->save())
        {
            return ['resulte' => '等待验证'];

        }else {
            throw new NotFoundHttpException('请求的数据不存在或您的权限不足.');
        }

     }

    /**
     *  查看用户下面绑定的支付信息
     *
     */
    public function actionViews()
    {
        $model = $this->modelClass::find()
            ->where(['member_id' => Yii::$app->user->identity->member_id])
            ->asArray()
            ->all();

        return $model;
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

    /**
     * 返回模型
     *
     * @param $id
     * @return mixed
     */
    protected function findModel($id)
    {
        if ($id && ($model = MemberPrivacy::findOne(['member_id' => $id])))
        {
            return $model;
        }
        throw new \yii\web\BadRequestHttpException('不存在此数据');

    }

    /**
     * 返回模型
     *
     * @param $id
     * @return mixed
     */
    protected function findModels($id)
    {
        if ($id && ($model = MemberPrivacy::findOne(['member_id' => $id])))
        {
            return $model;

        }else{
            $model = new MemberPrivacy();
            $model->attributes = Yii::$app->request->post();
            if ($model->validate())
            {
                if ($user = $model->bind()){
                    return ['resulte' => '下一步'];
                }
            }
        }
        

    }


}