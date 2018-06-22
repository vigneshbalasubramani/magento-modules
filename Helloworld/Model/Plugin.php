<?php

namespace Zilker\Helloworld\Model;

class Plugin {
    protected $logger;
    protected $helperData;
    public function __construct(\Psr\Log\LoggerInterface $logger,
    \Zilker\Helloworld\Helper\Data $helperData) {
        $this->logger = $logger;
        $this->helperData = $helperData;
    }

    public function beforeAddProduct(\Magento\Checkout\Model\Cart $subject, $productInfo, $requestInfo = null) {
        $prefix = $this -> helperData -> getGeneralConfig('display_text');
        $isLogEnabled = $this -> helperData ->getGeneralConfig('enable');

        if($isLogEnabled == 1) {
            $this->logger->info($prefix . ' - From plugin : ' . $productInfo->getName());
        }
        return array($productInfo, $requestInfo);
    }

    // public function afterAddProduct($subject, $result) {
    //     $this->logger->info($result->productId);
    //     $this->logger->info('hello from plugin');
    //     return $result;
    // }

}

?>