<?php

namespace Hestec\SisowMethod;

use \Omnipay\Sisow\Gateway;
use SilverStripe\Omnipay\GatewayInfo;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\SiteConfig\SiteConfig;

class SisowMethod extends DataObject {

    private static $table_name = 'HestecSisowMethod';

    private static $singular_name = 'Sisow method';
    private static $plural_name = 'Sisow methods';

    private static $db = array(
        'MethodKey' => 'Varchar()',
        'MethodTitle' => 'Varchar()',
        'Enabled' => 'Boolean',
        'SortOrder' => 'Int'
    );

    private static $default_sort='SortOrder';

    private static $defaults = array(
        'Enabled' => true
    );

    private static $has_one = array(
        'SiteConfig' => SiteConfig::class
    );

    private static $summary_fields = array(
        'MethodKey',
        'MethodTitle',
        'Enabled.Nice'
    );

    function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['MethodKey'] = _t("SisowMethod.METHODKEY", "Method key");
        $labels['MethodTitle'] = _t("SisowMethod.METHODTITLE", "Method title");
        $labels['Enabled.Nice'] = _t("SisowMethod.ENABLED", "Enabled");

        return $labels;
    }

    public function getCMSFields() {

        // Field labels
        $l = $this->fieldLabels();

        // get the Sisow parameters from \mysite\_config\payment.yml
        $pars = GatewayInfo::getParameters('Sisow');

        // call the fetchPaymentMethods function in \vendor\hestec\omnipay-sisow\src\Omnipay\Sisow\Gateway.php
        // the Gateway class is add with use \Omnipay\Sisow\Gateway; on top of this file
        $gw = new Gateway();
        $result = $gw->fetchPaymentMethods($pars)->send();

        $methods = Array();
        foreach ($result->getData()->merchant->payments->payment as $node){

            // if the MethodKey is already in the dataobject, don't show it anymore
            if (SisowMethod::get()->filter('MethodKey', $node->__toString())->count() == 0 || $this->MethodKey == $node->__toString()){
                $methods[$node->__toString()] = $node->__toString();
            }

        }

        $SisowPaymentField = DropdownField::create('MethodKey', $l['MethodKey'], $methods);

        $MethodTitleField = TextField::create('MethodTitle', $l['MethodTitle']);
        $MethodTitleField->setDescription(_t("SisowMethod.METHODTITLE_DESCRIPTION", "The method title appears on the website."));
        $EnabledField = CheckboxField::create('Enabled', _t("SisowMethod.ENABLED", "Enabled"));

        $fields = new FieldList(
            $SisowPaymentField,
            $MethodTitleField,
            $EnabledField
        );

        return $fields;

    }

    public function getCMSValidator() {

        return new RequiredFields(array(
            'MethodKey',
            'MethodTitle'
        ));
    }

}