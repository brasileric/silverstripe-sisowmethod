<?php

namespace Hestec\SisowMethod;

use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Permission;

class SisowSiteConfig extends DataExtension {

    private static $has_many = array(
        'SisowMethods' => SisowMethod::class
    );

    public function updateCMSFields(FieldList $fields)
    {

        $SisowMethodsGridField = new GridField(
            'SisowMethods',
            _t("SiteConfig.SISOWMETHODS", "Sisow Methods"),
            $this->owner->SisowMethods(),
            GridFieldConfig::create()
                ->addComponent(new GridFieldToolbarHeader())
                ->addComponent(new GridFieldAddNewButton("toolbar-header-right"))
                ->addComponent(new GridFieldSortableHeader())
                ->addComponent(new GridFieldDataColumns())
                ->addComponent(new GridFieldPaginator(50))
                ->addComponent(new GridFieldEditButton())
                ->addComponent(new GridFieldDeleteAction())
                ->addComponent(new GridFieldDetailForm())
                ->addComponent(new GridFieldFilterHeader())
                ->addComponent(new GridFieldOrderableRows('SortOrder'))
        );

        if (Permission::check('ADMIN')) {
            $fields->addFieldsToTab("Root." . _t("SiteConfig.SISOWMETHODS", "Sisow Methods"), array(
                $SisowMethodsGridField
            ));

        }

    }

}