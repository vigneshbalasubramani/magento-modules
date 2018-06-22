<?php

namespace Zilker\Preference\Block;

use Magento\Theme\Block\Html;

class Breadcrumbs extends Html\Breadcrumbs
{
    protected $_logger;

    public function __construct(\Psr\Log\LoggerInterface $loggerInterface) {
        $this->_logger = $loggerInterface;
    }

    public function getCrumbs()
    {
        return $this->_crumbs;
    }

    protected function _toHtml()
    {
        $this->_logger->info(array_keys($this->_crumbs)[0]);
        $this->setModuleName($this->extractModuleName('Magento\Theme\Block\Html\Breadcrumbs'));
        return parent::_toHtml();
    }
}

?>