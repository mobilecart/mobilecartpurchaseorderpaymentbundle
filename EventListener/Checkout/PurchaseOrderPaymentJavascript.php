<?php

namespace MobileCart\PurchaseOrderPaymentBundle\EventListener\Checkout;

use Symfony\Component\EventDispatcher\Event;

class PurchaseOrderPaymentJavascript
{
    protected $paymentService;

    protected $event;

    public function setPaymentMethodService($paymentService)
    {
        $this->paymentService = $paymentService;
        return $this;
    }

    public function getPaymentMethodService()
    {
        return $this->paymentService;
    }

    protected function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    protected function getEvent()
    {
        return $this->event;
    }

    public function getReturnData()
    {
        return $this->getEvent()->getReturnData()
            ? $this->getEvent()->getReturnData()
            : [];
    }

    public function onCheckoutViewReturn(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();

        if (!isset($returnData['javascripts'])) {
            $returnData['javascripts'] = [];
        }

        $returnData['javascripts'][] = [
            'js_template' => 'MobileCartPurchaseOrderPaymentBundle:Checkout:payment_js.html.twig',
            'data' => [
                'code' => $this->getPaymentMethodService()->getCode(),
            ],
        ];

        $event->setReturnData($returnData);
    }
}
