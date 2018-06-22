<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zilker\Helloworld\Controller\Account;

class Create extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Api\OrderCustomerManagementInterface
     */
    protected $orderCustomerService;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Api\OrderCustomerManagementInterface $orderCustomerService
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->orderCustomerService = $orderCustomerService;
        parent::__construct($context);
    }

    /**
     * Execute request
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_objectManager->get(\Magento\Framework\Controller\Result\JsonFactory::class)->create();

        if ($this->customerSession->isLoggedIn()) {
            return null;
        }
        $orderId = $this->checkoutSession->getLastOrderId();
        if (!$orderId) {
            return $resultJson->setData(
                [
                    'errors' => true,
                    'message' => __('Your session has expired')
                ]
            );
        }
        try {
            $this->orderCustomerService->create($orderId);
            $this->messageManager->addSuccessMessage('A letter with further instructions will be sent to your email.');
            return $resultJson->setData(
                [
                    'errors' => false,
                    'message' => __('A letter with further instructions will be sent to your email.')
                ]
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}
