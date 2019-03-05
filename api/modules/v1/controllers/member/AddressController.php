<?php
namespace api\modules\v1\controllers\member;

use api\controllers\UserAuthController;
use common\models\member\Address;

/**
 * 收货地址
 *
 * Class AddressController
 * @package api\modules\v1\controllers\member
 * @property \yii\db\ActiveRecord $modelClass
 * @author cx
 */
class AddressController extends UserAuthController
{
    public $modelClass = 'common\models\member\Address';
}
