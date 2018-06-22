<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Zilker\Queues\Model;

use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Framework\DB\Adapter\ConnectionException;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Framework\Exception\TemporaryStateExceptionInterface;

/**
 * Class Consumer
 */
class Consumer
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\BulkOperations\Model\OperationManagement
     */
    private $operationManagement;

    /**
     * Consumer constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Bulk\OperationManagementInterface $operationManagement
    ) {
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->operationManagement = $operationManagement;
    }

    /**
     * Processing operation for update price
     *
     * @param \Magento\AsynchronousOperations\Api\Data\OperationInterface $operation
     * @return void
     */
    public function processOperation(string $data)
    {
        $status = OperationInterface::STATUS_TYPE_COMPLETE;
        $errorCode = null;
        $message = null;
        try {
            //add here your own logic for async opertions
            $this->logger->info('From Message queue: ' . $data);
        } catch (\Zend_Db_Adapter_Exception  $e) {
            //here sample how to process exceptions if they occured
            $this->logger->critical($e->getMessage());
            //you can add here your own type of exception when operation can be retried
            if (
                $e instanceof LockWaitException
                || $e instanceof DeadlockException
                || $e instanceof ConnectionException
            ) {
                $status = OperationInterface::STATUS_TYPE_RETRIABLY_FAILED;
                $errorCode = $e->getCode();
                $message = __($e->getMessage());
            } else {
                $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
                $errorCode = $e->getCode();
                $message = __('Sorry, something went wrong during product prices update. Please see log for details.');
            }

        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
            $status = ($e instanceof TemporaryStateExceptionInterface) ? OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED : OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();

            $message = $e->getMessage();
            unset($unserializedData['entity_link']);
            $serializedData = $this->jsonHelper->jsonEncode($unserializedData);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = $e->getMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = __('Sorry, something went wrong during product prices update. Please see log for details.');
        }

        //update operation status based on result performing operation(it was successfully executed or exception occurs
        $this->operationManagement->changeOperationStatus(
            $operation->getId(),
            $status,
            $errorCode,
            $message,
            $serializedData
        );
    }
}