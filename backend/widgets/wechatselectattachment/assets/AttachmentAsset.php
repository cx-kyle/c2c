<?php
namespace backend\widgets\wechatselectattachment\assets;

use yii\web\AssetBundle;

/**
 * Class AttachmentAsset
 * @package backend\widgets\wechatselectattachment\assets
 * @author cx
 */
class AttachmentAsset extends AssetBundle
{
    public $sourcePath = '@backend/widgets/wechatselectattachment/resources/';

    public $css = [
    ];

    public $js = [
        'list.js',
    ];

    public $depends = [
    ];
}