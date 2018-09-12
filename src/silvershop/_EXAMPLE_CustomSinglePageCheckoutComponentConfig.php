<?php

use SilverStripe\Omnipay\GatewayInfo;

class CustomSinglePageCheckoutComponentConfig extends CheckoutComponentConfig
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
        $this->addComponent(CustomerDetailsCheckoutComponent::create());
        $this->addComponent(ShippingAddressCheckoutComponent::create());
        $this->addComponent(BillingAddressCheckoutComponent::create());
        $this->addComponent(NotesCheckoutComponent::create());
        if (Checkout::member_creation_enabled() && !Member::currentUserID()) {
            $this->addComponent(MembershipCheckoutComponent::create());
        }
        if (count(GatewayInfo::getSupportedGateways()) > 1) {
            $this->addComponent(SisowPaymentCheckoutComponent::create());
        }
        $this->addComponent(TermsCheckoutComponent::create());
    }
}