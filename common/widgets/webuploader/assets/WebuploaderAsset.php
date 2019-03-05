<?php
namespace common\widgets\webuploader\assets;

use yii\web\AssetBundle;

/**
 * Class WebuploaderAsset
 * @package common\widgets\webuploader\assets
 * @author cx
 */
class WebuploaderAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets/webuploader/resources/webuploader/';

    public $css = [
       // 'webuploader.css',
    ];

    public $js = [
        'webuploader.min.js',
    ];

    public $depends = [
        // 'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
    ];
}