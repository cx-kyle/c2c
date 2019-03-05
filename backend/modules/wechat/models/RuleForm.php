<?php
namespace backend\modules\wechat\models;

use common\models\wechat\Rule;

/**
 * Class RuleForm
 * @package backend\modules\wechat\models
 * @author cx
 */
class RuleForm extends Rule
{
    public $keyword;

    /**
     * @return array
     */
    public function rules()
    {
        $rule = parent::rules();
        $rule[] = [['keyword'], 'required', 'message' => '关键字不能为空'];

        return $rule;
    }
}
