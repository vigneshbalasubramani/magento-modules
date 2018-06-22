<?php
namespace Zilker\Helloworld\Observer;

use Magento\Framework\Event\ObserverInterface;

class MyObserver implements ObserverInterface
{
    protected $logger;
    protected $helperData;

    public function __construct(\Psr\Log\LoggerInterface $logger,
    \Magento\Framework\MessageQueue\Publisher $publisher,
    \Zilker\Helloworld\Helper\Data $helperData
    )
 {
     $this -> logger = $logger;
     $this -> helperData = $helperData;
     $this -> publisher = $publisher;
 //Observer initialization code...
 //You can use dependency injection to get any class this observer may need.
 }

public function execute(\Magento\Framework\Event\Observer $observer)
 {
 //Observer execution code...
    $prefix = $this -> helperData -> getGeneralConfig('display_text');
    $isLogEnabled = $this -> helperData ->getGeneralConfig('enable');

    if($isLogEnabled == 1) {
        $this -> logger -> info($prefix . ' - From observable : ' . $observer->getProduct()->getName());
    }
    $this -> publisher -> publish('mtopic', $observer->getProduct()->getName());
}
}

?>