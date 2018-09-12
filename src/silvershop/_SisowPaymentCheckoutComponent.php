<?php

use SilverStripe\Omnipay\GatewayInfo;

class SisowPaymentCheckoutComponent extends CheckoutComponent
{

    public function getFormFields(Order $order)
    {

        Requirements::css("sisow-method/css/sisow.css");
        Requirements::javascript("sisow-method/js/sisow.js");

        $gateways = GatewayInfo::getSupportedGateways();

        $result = Array();
        foreach ($gateways as $key => $node) {

            if ($key == "Sisow") {

                // get Sisow methods from the module sisow-method
                $sisowgw = SisowMethod::get()->filter('Enabled', true)->sort('SortOrder');

                foreach ($sisowgw as $gw) {

                    $result["Sisow_" . $gw->MethodKey] = $gw->MethodTitle;

                }

            } else {
                $result[$key] = $node;
            }

        };

        $IdealBanksSource = $this->getIdealBanks();

        $GatewayField = OptionsetField::create('PaymentMethod', _t('SisowMethod.CHOOSE_PAYMENT_METHOD', "Choose your payment method"), $result, array_keys($gateways));
        $GatewayField->addExtraClass("optionset-gateway");
        $GatewayField->setTemplate('SisowOptionsetField');

        $IdealBankField = DropdownField::create('issuer', _t('SisowMethod.CHOOSE_YOUR_BANK', "Choose your bank for iDEAL"), $IdealBanksSource);
        $IdealBankField->addExtraClass("show-ideal-banks");
        $IdealBankField->setEmptyString('('._t('SisowMethod.SELECT_BANK', "Select bank").')');

        $PaymentHeaderField = HeaderField::create('PaymentHeaderField', _t('SisowMethod.PAYMENT_METHOD', "Payment method"));

        $fields = FieldList::create(array(
            $PaymentHeaderField,
            $GatewayField,
            $IdealBankField,
        ));

        return $fields;
    }

    public function getRequiredFields(Order $order)
    {
        if (count(GatewayInfo::getSupportedGateways()) > 1) {
            return array();
        }

        return array('PaymentMethod');
    }

    public function validateData(Order $order, array $data)
    {
        $result = ValidationResult::create();
        if (!isset($data['PaymentMethod'])) {
            $result->error(
                _t('PaymentCheckoutComponent.NoPaymentMethod', "Payment method not provided"),
                "PaymentMethod"
            );
            throw new ValidationException($result);
        }
        if ($data['PaymentMethod'] == "Sisow_ideal" && $data['issuer'] < 1){
            $result->error(
                _t('PaymentCheckoutComponent.NoIssuer', "For the iDEAL method you have to choose a bank"),
                "PaymentMethod"
            );
            throw new ValidationException($result);
        }


        if (substr($data['PaymentMethod'], 0,6) == "Sisow_"){
            $gw = "Sisow";
        }else{
            $gw = $data['PaymentMethod'];
        }

        $methods = GatewayInfo::getSupportedGateways();
        if (!isset($methods[$gw])) {
            $result->error(_t('PaymentCheckoutComponent.UnsupportedGateway', "Gateway not supported"), "PaymentMethod");
            throw new ValidationException($result);
        }
    }

    public function getData(Order $order)
    {
        return array(
            'PaymentMethod' => Checkout::get($order)->getSelectedPaymentMethod(),
        );
    }

    public function setData(Order $order, array $data)
    {

        if (substr($data['PaymentMethod'], 0,6) == "Sisow_"){
            $gw = "Sisow";
            // this session are used fro the extra needed Sisow paramaters, added to the gateway in SisowGatewayUpdate.php
            Session::set('sisow_issuer', $data['issuer']);
            // the sisow payment method, in at Sisow called "payment"
            Session::set('sisow_method', substr($data['PaymentMethod'], 6));
        }else{
            $gw = $data['PaymentMethod'];
        }
        if (isset($gw)){
            Checkout::get($order)->setPaymentMethod($gw);
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

    public function getIdealBanks()
    {

        if ($this->isTestMode('Sisow') === true){
            $sisowtest = "true";
        }

        $service = new RestfulService("https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx", 0);
        $response = $service->request('/DirectoryRequest?test='.$sisowtest, 'GET');

        $xml = simplexml_load_string($response->getBody());
        $result = array();
        foreach( $xml->directory->issuer as $node )
        {
            $result[ $node->issuerid->__toString() ] = $node->issuername->__toString();
        }

        return $result;

    }

}
