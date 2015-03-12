<?php

namespace uran1980\yii\mdEpicEditor;

class MdEpicEditorAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/epiceditor/epiceditor';
    public $js = [
        'js/epiceditor.min.js',
    ];
    public $css = [
        'themes/base/epiceditor.css',
        'themes/editor/epic-light.css',
        'themes/preview/github.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
