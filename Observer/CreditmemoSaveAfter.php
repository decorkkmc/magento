<?php

namespace Tamara\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order\Creditmemo;
use Tamara\Checkout\Gateway\Config\BaseConfig;

class CreditmemoSaveAfter extends AbstractObserver
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var BaseConfig
     */
    protected $config;

    /**
     * @var \Tamara\Checkout\Helper\Refund
     */
    protected $refundHelper;

    public function __construct(
        Logger $logger,
        BaseConfig $config,
        \Tamara\Checkout\Helper\Refund $refundHelper
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->refundHelper = $refundHelper;
    }

    public function execute(Observer $observer)
    {
        $this->logger->debug(['Tamara - Start to creditmemo']);

        if (!$this->config->getTriggerActions()) {
            $this->logger->debug(['Tamara - Turned off the trigger actions']);
            return;
        }

        /** @var Creditmemo $creditMemo */
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $this->refundHelper->refundOrderByCreditMemo($creditMemo);
        $this->logger->debug(['Tamara - End to creditmemo']);
    }
}