<?php
/**
 * Created by PhpStorm.
 * User: 24055
 * Date: 2019/2/14
 * Time: 10:54
 */

namespace api\modules\v1\controllers\member;

use yii;
use common\helpers\ResultDataHelper;
use yii\web\NotFoundHttpException;
use api\controllers\OnAuthController;
use common\enums\StatusEnum;
use common\models\member\Orders;
use common\models\member\Pendings;
use common\models\member\MemberInfo;
class OrderController extends OnAuthController
{
    public $modelClass = 'common\models\member\Orders';

    /**
     * 个人未完成订单列表
     *
     */
    public function actionUndone(){
        $user = Yii::$app->user->identity->member_id;
        $model = $this->modelClass::find()
            ->alias("A")
            ->select(['A.*','rf_pendings.asset_price'])
            ->where(['or',['A.buyer' => $user],['A.seller' =>$user]])
            ->andWhere(['<','A.status',4])
            ->andWhere(['>','A.status',0])
            ->joinwith('bank')
            ->joinwith('pending',false)
            ->orderBy('A.update_at desc')
            ->limit(10)
            ->asArray()
            ->all();

        foreach($model as $k=>$v){
            $user_id = explode(',',$v['buyer']);
            $sell_id =  explode(',',$v['seller']);

            
            $a=Orders::find()
            ->select(['count(*) as quantity'])
            ->where(['or',['buyer' => $user_id],['seller' =>$user_id]])
            ->asArray()
            ->one();
            $b=Orders::find()
            ->select(['count(*) as volume'])
            ->where(['or',['buyer' => $user_id],['seller' =>$user_id]])
            ->andWhere(['status'=>4])
            ->asArray()
            ->one();

            //卖者信息
            $q=Orders::find()
            ->select(['count(*) as quantity'])
            ->where(['or',['buyer' => $user_id],['seller' =>$user_id]])
            ->asArray()
            ->one();

            $e=Orders::find()
            ->select(['count(*) as volume'])
            ->where(['or',['buyer' => $user_id],['seller' =>$user_id]])
            ->andWhere(['status'=>4])
            ->asArray()
            ->one();


            $d = MemberInfo::find()
            ->select(['username','created_at'])
            ->where(['id'=>$user_id,'status'=>1])
            ->asArray()
            ->one();
            $r = MemberInfo::find()
            ->select(['username','created_at'])
            ->where(['id'=>$user_id,'status'=>1])
            ->asArray()
            ->one();
            $percentage = round(($b['volume']/$a['quantity'])*100,2)."%";
            $percentage2 = round(($e['volume']/$q['quantity'])*100,2)."%";
            $dd=['user_id'=>$sell_id[0],'quantity'=>$a['quantity'],'volume'=>$b['volume'],'percentage'=>$percentage,'username'=>$d['username'],'create_at'=>$d['created_at']];
            $ww =['user_id'=>$user_id[0],'quantity'=>$q['quantity'],'volume'=>$e['volume'],'percentage'=>$percentage2,'username'=>$r['username'],'create_at'=>$r['created_at']];


            if($sell_id[0] == $user){
                $model[$k]['sale_type'] = 2;
                $model[$k]['opponent'] = $dd;
            }
            if($user_id[0] == $user){
                $model[$k]['sale_type'] = 1;
                $model[$k]['opponent'] = $ww;
            }

            

            //p($v);
        }
        //p($dd);
        // foreach($model as $k =>$v){
        //     $model[$k]['buyerinfo'] = $dd;
        //     $model[$k]['sellerinfo'] = $ww;
        // }    
        return $model;
    }

    /**
     * 个人已完成订单
     */
    public function actionCompleted(){

        $model = $this->modelClass::find()
            ->alias("A")
            ->where(['or',['buyer' => Yii::$app->user->identity->member_id],['seller' =>Yii::$app->user->identity->member_id]])
            ->andWhere(['status'=>4])
            ->limit(10)
            ->all();
        return $model;
    }


    /**
     * 个人取消订单
     */

    public function actionExpired(){
        $model = $this->modelClass::find()
            ->alias("A")
            ->where(['or',['buyer' => Yii::$app->user->identity->member_id],['seller' =>Yii::$app->user->identity->member_id]])
            ->andWhere(['status'=>0])
            ->limit(10)
            ->all();
        return $model;
    }

    

    /**
     * @return string
     * @throws yii\web\BadRequestHttpException
     * 修改订单状态
     */

    public function actionSetstatus()
    {
        header("Access-Control-Allow-Origin: *");
        header('Content-type:text/json');
        $request = Yii::$app->request;
        if ($request->isPut)  { /* 请求方法是 PUT */
            $param = $request->getBodyParam('id');
            $status = $request->getBodyParam('status');
        }
        $status = $request->getBodyParam('status');
        $model = $this->findModel($param);
        if($model->status ==2){
            $res = ['code'=>200,'message'=>'买家已付款'];
            
            echo json_encode($res) ;
            exit;
        }
        if($model->status ==3){
            $res = ['code'=>200,'message'=>'卖家已收款'];
            echo json_encode($res) ;
            exit;
        }
        $model->status = $status;
        $model->save(false);

        if($model->status == 2){
            $return = '买方已确认付款';
        }elseif ($model->status == 3){
            $return = '卖方已确定收款';
        }elseif ($model->status == 4){
            $return = '买方确认收币,完成交易';
        }
        else{
            $return = '';
        }

        return $return;
    }


    /**
     * 取消订单
     */

    public function actionCancel(){

        $request = Yii::$app->request;
        if ($request->isPut)  { /* 请求方法是 PUT */
            $param = $request->getBodyParam('id');
        }
        $model = $this->findModel($param);
        $connection = Yii::$app->db->beginTransaction();
        try{
            $model->status = 0;
            $pending = $this->findModel2($model->pending_id);
            $pending->asset_num = $pending->asset_num + $model->asset_num;

            $model->save(false);
            $pending->save(false);
            $connection->commit();
            return ['resulte' => '已取消订单'];
        }
        catch (\Exception $e){
            $connection->rollBack();
            return ["status"=>400,"msg"=>"订单取消异常,请稍后再试"] ;
            
        }

    }

    public function actionView($id)
    {
        $model = $this->modelClass::find()
            ->alias("A")
            ->select(['A.pending_id','A.create_at','A.asset_num','A.buyer'])
            ->where(['A.id'=>$id])
            ->with('user')
//            ->joinwith('pending')
            ->limit(10)
            ->asArray()
            ->all();
        return $model;

    }


    /**
     * 根据挂单号查询是否有订单
     *
     */
    public function actionSearch($pending_id)
    {
        $model = $this->modelClass::find()
            ->alias("A")
            ->where(['A.pending_id'=>$pending_id])
            ->andWhere(['A.status'=>[1,2,3]])
            ->joinwith('bank')
            ->asArray()
            ->one();
        //$model['create_at'] = date("Y-m-d H:i",$model['create_at']);
        //$model['update_at'] = date("Y-m-d H:i",$model['update_at']);
        return $model;
    }


    /**
     *  增加挂单
     *
     */
    public function actionCreate()
    {
        $model = new Orders();
        $model->attributes = Yii::$app->request->post();

        $user = Yii::$app->user->identity->member_id;
        $data = Orders::find()
            ->where(['or',['buyer'=>$user],['seller'=>$user]])
            ->andWhere(['pending_id'=>Yii::$app->request->post()['pending_id']])
            ->andWhere(['status'=>[1,2,3,4]])
            ->asArray()
            ->one();
        if($data && count($data)>0){
            $res = ['code'=>200,'message'=>'用户已下单'];
            header("Access-Control-Allow-Origin: *");
            header('Content-type:text/json');
            echo json_encode($res) ;
            exit;
        }else{
            if ($model->validate())
            {
                if ($user = $model->bind()){
                    return ['resulte' => '已生成订单'];
                }
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
        if (($model = Orders::findOne(['id'=>$id,'status'=>[1,2,3]])))
        {
            return $model;
        }

        throw new \yii\web\BadRequestHttpException('不存在此数据');

    }

    protected function findModel2($id)
    {
        if (($model = Pendings::findOne($id)))
        {
            return $model;
        }

        throw new \yii\web\BadRequestHttpException('不存在此数据');

    }


}