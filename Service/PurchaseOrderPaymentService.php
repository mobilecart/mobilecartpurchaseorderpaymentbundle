<?php

namespace MobileCart\PurchaseOrderPaymentBundle\Service;

use MobileCart\CoreBundle\Payment\PaymentMethodServiceInterface;

/**
 * Class PurchaseOrderPaymentService
 * @package MobileCart\PurchaseOrderPaymentBundle\Service
 */
class PurchaseOrderPaymentService
    implements PaymentMethodServiceInterface
{
    protected $formFactory;

    protected $form;

    protected $action;

    protected $defaultAction = self::ACTION_PURCHASE;

    protected $code = 'po';

    protected $label = 'Purchase Order';

    protected $isTestMode = false;

    protected $isRefund = false;

    protected $isSubmission = false;

    protected $paymentData = [];

    protected $orderData = [];

    protected $orderPaymentData = [];

    protected $isAuthorized = false;

    protected $isCaptured = false;

    protected $isPurchased = false;

    protected $purchaseRequest;

    protected $purchaseResponse;

    protected $authorizeRequest;

    protected $authorizeResponse;

    protected $captureRequest;

    protected $captureResponse;

    protected $tokenCreateRequest;

    protected $tokenCreateResponse;

    protected $tokenPaymentRequest;

    protected $tokenPaymentResponse;

    protected $subscribeRecurringRequest;

    protected $subscribeRecurringResponse;

    protected $confirmation = '';

    protected $ccFingerprint = '';

    protected $ccLastFour = '';

    protected $ccType = '';

    /**
     * @param $formFactory
     * @return $this
     */
    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    public function setDefaultAction($action)
    {
        if (!$this->supportsAction($action)) {
            throw new \InvalidArgumentException("Un-Supported Payment Action specified");
        }

        $this->defaultAction = $action;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * @return array
     */
    public function supportsActions()
    {
        return [
            self::ACTION_AUTHORIZE,
            self::ACTION_CAPTURE,
            self::ACTION_PURCHASE,
        ];
    }

    /**
     * @param $action
     * @return bool
     */
    public function supportsAction($action)
    {
        return in_array($action, $this->supportsActions());
    }

    /**
     * @param $action
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setAction($action)
    {
        if (!$this->supportsAction($action)) {
            throw new \InvalidArgumentException("Invalid Payment Action Specified");
        }

        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return isset($this->action)
            ? $this->action
            : $this->getDefaultAction();
    }

    /**
     * @return $this
     */
    public function buildForm()
    {
        $formTypeClass = 'MobileCart\PurchaseOrderPaymentBundle\Form\PurchaseOrderPaymentType';
        $this->setForm($this->getFormFactory()->create($formTypeClass));
        return $this;
    }

    /**
     * @param $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param $isTestMode
     * @return $this
     */
    public function setIsTestMode($isTestMode)
    {
        $this->isTestMode = ($isTestMode != '0' && $isTestMode != 'false');
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsTestMode()
    {
        return $this->isTestMode;
    }

    /**
     * @param $isRefund
     * @return $this
     */
    public function setIsRefund($isRefund)
    {
        $this->isRefund = $isRefund;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRefund()
    {
        return $this->isRefund;
    }

    /**
     * @param $isSubmission
     * @return $this
     */
    public function setIsSubmission($isSubmission)
    {
        $this->isSubmission = $isSubmission;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsSubmission()
    {
        return $this->isSubmission;
    }

    /**
     * @param $paymentData
     * @return $this
     */
    public function setPaymentData($paymentData)
    {
        $this->paymentData = $paymentData;
        return $this;
    }

    /**
     * @return array
     */
    public function getPaymentData()
    {
        return $this->paymentData;
    }

    /**
     * @param $orderData
     * @return $this
     */
    public function setOrderData($orderData)
    {
        $this->orderData = $orderData;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderData()
    {
        return $this->orderData;
    }

    /**
     * @param $confirmation
     * @return $this
     */
    public function setConfirmation($confirmation)
    {
        $this->confirmation = $confirmation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirmation()
    {
        return $this->confirmation;
    }

    /**
     * @param $ccFingerprint
     * @return $this
     */
    public function setCcFingerprint($ccFingerprint)
    {
        $this->ccFingerprint = $ccFingerprint;
        return $this;
    }

    /**
     * @return string
     */
    public function getCcFingerprint()
    {
        return $this->ccFingerprint;
    }

    /**
     * @param $ccLastFour
     * @return $this
     */
    public function setCcLastFour($ccLastFour)
    {
        $this->ccLastFour = $ccLastFour;
        return $this;
    }

    /**
     * @return string
     */
    public function getCcLastFour()
    {
        return $this->ccLastFour;
    }

    /**
     * @param $ccType
     * @return $this
     */
    public function setCcType($ccType)
    {
        $this->ccType = $ccType;
        return $this;
    }

    /**
     * @return string
     */
    public function getCcType()
    {
        return $this->ccType;
    }

    /**
     * @return array
     */
    public function extractOrderPaymentData()
    {
        $orderData = $this->getOrderData();
        $paymentData = $this->getPaymentData();

        return [
            'code' => $this->getCode(),
            'label' => $this->getLabel(),
            'base_currency' => $orderData['base_currency'],
            'base_amount' => $orderData['base_total'],
            'currency' => $orderData['currency'],
            'amount' => $orderData['total'],
            'reference_nbr' => $paymentData['reference_nbr'],
        ];
    }

    //// Purchase

    public function purchase()
    {
        $this->buildPurchaseRequest()
            ->sendPurchaseRequest();

        return $this;
    }

    /**
     * @return $this
     */
    public function buildPurchaseRequest()
    {
        $orderData = $this->getOrderData();
        $paymentData = $this->getPaymentData();

        $amount = $orderData['total'];
        $currency = $orderData['currency'];

        $this->setPurchaseRequest([
            'amount' => $amount,
            'currency' => $currency,
            'card' => $paymentData,
        ]);

        return $this;
    }

    /**
     * @param $request
     * @return $this
     */
    public function setPurchaseRequest($request)
    {
        $this->purchaseRequest = $request;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPurchaseRequest()
    {
        return $this->purchaseRequest;
    }

    /**
     * @return $this
     */
    public function sendPurchaseRequest()
    {
        $this->setIsPurchased(1);
        return $this;
    }

    /**
     * @param $purchaseResponse
     * @return $this
     */
    public function setPurchaseResponse($purchaseResponse)
    {
        $this->purchaseResponse = $purchaseResponse;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPurchaseResponse()
    {
        return $this->purchaseResponse;
    }

    public function setIsPurchased($isPurchased)
    {
        $this->isPurchased = $isPurchased;
        return $this;
    }

    public function getIsPurchased()
    {
        return $this->isPurchased;
    }

    //// Authorize

    /**
     * @return $this
     */
    public function authorize()
    {
        $this->setIsAuthorized(1);
        return $this;
    }

    public function buildAuthorizeRequest()
    {

    }

    public function setAuthorizeRequest($authorizeRequest)
    {
        $this->authorizeRequest = $authorizeRequest;
        return $this;
    }

    public function getAuthorizeRequest()
    {
        return $this->authorizeRequest;
    }

    public function sendAuthorizeRequest()
    {

    }

    public function setAuthorizeResponse($authorizeResponse)
    {
        $this->authorizeResponse = $authorizeResponse;
        return $this;
    }

    public function getAuthorizeResponse()
    {
        return $this->authorizeResponse;
    }

    /**
     * @param $yesNo
     * @return $this
     */
    public function setIsAuthorized($yesNo)
    {
        $this->isAuthorized = $yesNo;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsAuthorized()
    {
        return $this->isAuthorized;
    }

    /**
     * @return bool
     */
    public function authorizeAndCapture()
    {
        return $this->authorize() && $this->capture();
    }

    //// Capture (a pre-authorized transaction ONLY)

    /**
     * @return $this
     */
    public function capture()
    {
        $this->setIsCaptured(1);

        return $this;
    }

    public function buildCaptureRequest()
    {

        return $this;
    }

    public function setCaptureRequest($captureRequest)
    {
        $this->captureRequest = $captureRequest;
        return $this;
    }

    public function getCaptureRequest()
    {
        return $this->captureRequest;
    }

    public function sendCaptureRequest()
    {

        return $this;
    }

    public function setCaptureResponse($captureResponse)
    {
        $this->captureResponse = $captureResponse;
        return $this;
    }

    public function getCaptureResponse()
    {
        return $this->captureResponse;
    }

    /**
     * @param $isCaptured
     * @return $this
     */
    public function setIsCaptured($isCaptured)
    {
        $this->isCaptured = $isCaptured;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCaptured()
    {
        return $this->isCaptured;
    }
}
