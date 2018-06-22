<?php
namespace Zilker\Helloworld\Observer;

use Magento\Framework\Event\ObserverInterface;

class NewAccount implements \Magento\Framework\Event\ObserverInterface {
 
    protected $_logger;
    protected $_accountCreator;
    protected $_action;
 
    public function __construct(        
        \Psr\Log\LoggerInterface $loggerInterface,
        \Zilker\Helloworld\Controller\Account\Create $create
    ) { 
        $this->_logger = $loggerInterface;
        $this->_accountCreator = $create;
    }

public function execute(\Magento\Framework\Event\Observer $observer)
 {
 //Observer execution code...
    $this->_logger->info('observer is correct');
    $this->_accountCreator->execute();
    // $this->_logger->info(json_encode(get_class_methods($this->_accountCreator->messageManager)));
 }
}

?>