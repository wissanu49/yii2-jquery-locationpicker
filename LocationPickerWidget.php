<?php
namespace pigolab\locationpicker;

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;



/**
 * This is just an example.
 */
class LocationPickerWidget extends \yii\base\Widget
{
    /** @var string google map api key */
    public $key;
    public $options = [];
    public $clientOptions = [];
    public $clientEvents = [];
    
    public function init() {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if($this->key === null) {
            throw new InvalidConfigException("Invalid value for the property 'key'.");
        }

        $url = 'http://maps.google.com/maps/api/js?sensor=false&libraries=places&key=' . $this->key;
        $this->view->registerJsFile($url, [
            'position' => View::POS_END
        ]);
        //$this->view->registerJs('http://maps.google.com/maps/api/js?sensor=true&libraries=places&key=' . $this->key , View::POS_END);       
        LocationPickerAsset::register($this->view);
    }

    /**
     * Copy from jui widget
     * @param string $name
     * @param string $id
     */
    protected function registerClientOptions($name, $id)
    {
        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('#$id').$name($options);";
            $this->getView()->registerJs($js);
        }
    }

    /**
     * Copy from jui widget
     * @param string $name
     * @param string $id
     */
    protected function registerClientEvents($name, $id)
    {
        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                if (isset($this->clientEventMap[$event])) {
                    $eventName = $this->clientEventMap[$event];
                } else {
                    $eventName = strtolower($name . $event);
                }
                $js[] = "jQuery('#$id').on('$eventName', $handler);";
            }
            $this->getView()->registerJs(implode("\n", $js));
        }
    }
    public function run()
    {       
        echo Html::tag('div','',$this->options);
        $this->registerClientOptions('locationpicker', $this->getId());
        $this->registerClientEvents('locationpicker', $this->getId());

    }


}
