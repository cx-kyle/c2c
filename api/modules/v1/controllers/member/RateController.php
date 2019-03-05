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
use common\models\member\Rate;

/**
 * 挂单接口
 *
 * Class InfoController
 * @package api\modules\v1\controllers\member
 * @property \yii\db\ActiveRecord $modelClass
 * @author cx
 */

class RateController extends OnAuthController
{

    public $modelClass = 'common\models\member\Rate';

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $optional = ['index','view'];

    /**
     * 显示
     *
     */
    public function actionViews()
    {
        $member = Yii::$app->user->identity->member_id;
        $model = $this->modelClass::find()
            ->where(['member_id' => $member])
            ->asArray()
            ->all();

        if (!$model)
        {
            throw new NotFoundHttpException('请求的数据不存在.');
        }

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
        if (in_array($action, ['delete']))
        {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}