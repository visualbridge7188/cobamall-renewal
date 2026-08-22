<?php

namespace Bundle\Controller\Admin\Design;


use Bundle\Component\PlusShop\WeatherWidget\WeatherWidgetConfig;
use Bundle\Component\PlusShop\WeatherWidget\WeatherWidgetDao;
use Bundle\Controller\Admin\Controller;
use Component\Design\SkinBase;

class WeatherWidgetController extends Controller
{
    public function index()
    {
        $this->callMenu('design', 'widget', 'weatherWidget');

        $config = new WeatherWidgetConfig();
        $dao = new WeatherWidgetDao();

        $this->addCss([
            '../script/jquery/colorpicker-master/jquery.colorpicker.css',
        ]);
        $this->addScript([
            'jquery/colorpicker-master/jquery.colorpicker.js',
        ]);
        $this->setData('item', $dao->getData());
        $this->setData('locations', $config->getLocationKeys());

        // 반응형 스킨 경고 alert
        $this->setData('responsiveSkinAlertMessage', SkinBase::getResponsiveSkinAlertMessage());
    }
}
