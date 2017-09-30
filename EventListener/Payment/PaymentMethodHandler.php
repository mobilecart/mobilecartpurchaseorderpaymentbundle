<?php

namespace MobileCart\PurchaseOrderPaymentBundle\EventListener\Payment;

use MobileCart\CoreBundle\CartComponent\ArrayWrapper;
use MobileCart\CoreBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Event\Payment\FilterPaymentMethodCollectEvent;
use MobileCart\CoreBundle\Payment\PaymentMethodServiceInterface;

/**
 * Class PaymentMethodHandler
 * @package MobileCart\PurchaseOrderPaymentBundle\EventListener\Payment
 */
class PaymentMethodHandler
{
    /**
     * @var \MobileCart\CoreBundle\Payment\PaymentMethodServiceInterface
     */
    protected $paymentMethodService;

    /**
     * @var \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    protected $entityService;

    /**
     * @var \MobileCart\CoreBundle\Service\CartSessionService
     */
    protected $cartSessionService;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @param PaymentMethodServiceInterface $paymentMethodService
     * @return $this
     */
    public function setPaymentMethodService(PaymentMethodServiceInterface $paymentMethodService)
    {
        $this->paymentMethodService = $paymentMethodService;
        return $this;
    }

    /**
     * @return PaymentMethodServiceInterface
     */
    public function getPaymentMethodService()
    {
        return $this->paymentMethodService;
    }

    /**
     * @param $entityService
     * @return $this
     */
    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

    /**
     * @param $cartSessionService
     * @return $this
     */
    public function setCartSessionService($cartSessionService)
    {
        $this->cartSessionService = $cartSessionService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\CartSessionService
     */
    public function getCartSessionService()
    {
        return $this->cartSessionService;
    }

    /**
     * @param $isEnabled
     * @return $this
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Event Listener : top-level logic happens here
     *  build a request, handle the request, handle the response
     *
     * @param FilterPaymentMethodCollectEvent $event
     * @return mixed
     */
    public function onPaymentMethodCollect(FilterPaymentMethodCollectEvent $event)
    {
        if (!$this->getIsEnabled()) {
            return false;
        }

        $paymentMethodService = $this->getPaymentMethodService();
        $javascripts = [];

        $paymentMethodService->setAction(\MobileCart\CoreBundle\Payment\PaymentMethodServiceInterface::ACTION_PURCHASE);

        // trying to be more secure by not passing the full service into the view
        //  so , getting the service requires a flag to be set
        if ($event->getFindService()) {
            if ($event->getCode() == $this->getPaymentMethodService()->getCode()) {
                $event->setService($this->getPaymentMethodService());
                return true; // makes no difference
            }

            return false;
        }

        // todo : handle is_backend, is_frontend

        $javascripts[] = [
            'js_template' => 'MobileCartPurchaseOrderPaymentBundle:Checkout:payment_js.html.twig',
            'data' => [
                'code' => $this->getPaymentMethodService()->getCode(),
            ],
        ];

        /**
         * Main form builder logic
         */
        $form = $paymentMethodService->buildForm()
            ->getForm()
            ->createView();

        // todo: use a class which extends ArrayWrapper

        // trying to be more secure by not passing the full service into the view
        $wrapper = new ArrayWrapper();
        $wrapper->set('code', $paymentMethodService->getCode())
            ->set('label', $paymentMethodService->getLabel())
            ->set('action', $paymentMethodService->getAction())
            ->set('form', $form)
            ->set('javascripts', $javascripts);

        // payment form requirements
        // * dont conflict with parent form
        // * build form, populate if needed
        // * display using correct input parameters

        $event->addMethod($wrapper);
    }
}
