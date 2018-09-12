<?php

use SilverStripe\Omnipay\GatewayInfo;

class SisowGatewayUpdate extends Extension
{

    public function onBeforePurchase(array &$gatewayData)
    {
        $this->addSisowParams($gatewayData);
    }

    public function onAfterPurchase(){

        Session::clear('sisow_method');
        Session::clear('sisow_issuer');

    }

    private function addSisowParams(array &$gatewayData)
    {
        $payment = $this->owner->getPayment();
        if ($payment->Gateway == 'Sisow') {
            $gatewayData['paymentMethod'] = Session::get('sisow_method');
            $gatewayData['issuer'] = Session::get('sisow_issuer');
            $gatewayData['entrancecode'] = $payment->ID;
            $gatewayData['description'] = SiteConfig::current_site_config()->Title;
            if ($this->isTestMode("Sisow") === true){
                $gatewayData['testmode'] = "true";
            }
        }
    }

    public function isTestMode($gateway) {

        $istest = GatewayInfo::getParameters($gateway);
        if ($istest['testMode'] == true){
            return true;
        }else{
            return false;
        }

    }

}