<?php
namespace addons\RfArticle\wechat\controllers;

use Yii;
use common\controllers\AddonsBaseController;

/**
 * Class DefaultController
 * @package addons\RfArticle\wechat\controllers
 * @author cx
 */
class DefaultController extends AddonsBaseController
{
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        return $this->render('index',[

        ]);
    }
}