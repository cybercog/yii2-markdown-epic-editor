<?php

namespace uran1980\yii\widgets\mdEpicEditor;

use yii\widgets\InputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class MdEpicEditor extends InputWidget
{
    /**
     * @var boolean
     */
    public $isRegistered = false;

    /**
     * EpicEditor JS options you want to override
     * See all http://epiceditor.com/#options
     *
     * @var array
     */
    public $options = [];

    /**
     * Marked options (markdown parser used by EpicEditor)
     * See details https://github.com/chjj/marked
     *
     * @var array
     */
    public $optionsMarked = [];

    /**
     * @var array
     */
    public $textareaHtmlOptions = [];

    /**
     * @var array
     */
    public $divHtmlOptions = ['class' => 'epiceditordiv'];

    /**
     * Default options for EpicEditor widget. It will be overridden by $this->options
     * See all http://epiceditor.com/#options
     *
     * @var array
     */
    protected $defaultOptions = [
        'container'             => '',
        'textarea'              => '',
        'basePath'              => '',
        'clientSideStorage'     => false,
        'localStorageName'      => 'epiceditor',                                // maybe this should be overriden with ID-related val also
        'useNativeFullscreen'   => true,
        'file' => [
            'name'              => 'epiceditor',                                // maybe this should be overriden with ID-related val also
            'defaultContent'    => '',
            'autoSave'          => 100,
        ],
        'theme' => [
            'base'      => '/css/base/epiceditor.css',
            'preview'   => '/css/preview/github.css',
            'editor'    => '/css/editor/epic-light.css',
        ],
        'button' => [
            'preview'       => true,
            'fullscreen'    => true,
            'bar'           => 'auto',
        ],
        'focusOnLoad' => false,
        'shortcut' => [
            'modifier'      => 18,
            'fullscreen'    => 70,
            'preview'       => 80,
        ],
        'string' => [
            'togglePreview'     => 'Toggle Preview Mode',
            'toggleEdit'        => 'Toggle Edit Mode',
            'toggleFullscreen'  => 'Enter Fullscreen',
        ],
        'autogrow' => false,
    ];

    /**
     * Default Marked options (markdown parser used by EpicEditor)
     * See details https://github.com/chjj/marked
     *
     * @var array
     */
    protected $defaultOptionsMarked = [
        'gfm'           => 'true',
        'tables'        => 'true',
        'breaks'        => 'true',
        'pedantic'      => 'false',
        'sanitize'      => 'false',
        'smartLists'    => 'true',
        'smartypants'   => 'false',
    ];

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();

        $this->options       = ArrayHelper::merge($this->defaultOptions,        $this->options);
        $this->optionsMarked = ArrayHelper::merge($this->defaultOptionsMarked,  $this->optionsMarked);

        if ( empty($this->options['container']) ) {
            $this->options['container'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        }

        if ( empty($this->options['textarea']) ) {
            $this->options['textarea'] = 'textarea-' . $this->options['container'];
        }

        $bundles = $this->view->assetManager->bundles;
        $this->isRegistered = isset($bundles['uran1980\\yii\\widgets\\mdEpicEditor\\MdEpicEditorAsset']);

        $this
            ->registerAssetBundle()
            ->registerScript()
        ;
    }

    /**
     * @return \uran1980\yii\mdEpicEditor\MdEpicEditor
     */
    public function registerAssetBundle()
    {
        $bundle = MdEpicEditorAsset::register($this->getView());

        // set correct basePath
        $basePath                   = $bundle->getPublishedPath();
        $this->options['basePath']  = ArrayHelper::getValue($basePath, 1);

        return $this;
    }

    /**
     * @return \uran1980\yii\mdEpicEditor\MdEpicEditor
     */
    public function registerScript()
    {
        $jsonOptions        = Json::encode($this->options);
        $jsonOptionsMarked  = Json::encode($this->optionsMarked);
        $script = <<<SCRIPT
marked.setOptions('{$jsonOptionsMarked}');
new EpicEditor('{$jsonOptions}').load();
SCRIPT;

        $this->view->registerJs($script);

        return $this;
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        	//text area should be hidden, and DIV should be shown
            $this->textareaHtmlOptions = ArrayHelper::merge($this->textareaHtmlOptions, [
                'style' => 'display:none;',
            ]);
            $this->textareaHtmlOptions['id'] = $this->options['textarea'];

            if ($this->hasModel()) {
                echo Html::activeTextArea($this->model, $this->attribute, $this->textareaHtmlOptions);
            } else {
                echo Html::textArea($this->name, $this->value, $this->textareaHtmlOptions);
            }

            $this->divHtmlOptions = ArrayHelper::merge($this->divHtmlOptions, [
                'id' => $this->options['container'],
            ]);

            echo Html::tag('div', '', $this->divHtmlOptions);
    }


}
