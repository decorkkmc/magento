<?php

namespace Tamara\Checkout\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Sales\Api\OrderRepositoryInterface;
use Tamara\Checkout\Gateway\Config\BaseConfig;
use Tamara\Checkout\Model\Helper\CartHelper;

class Success extends Action
{
    protected $_pageFactory;

    /**
     * @var CartHelper;
     */
    private $cartHelper;

    protected $orderRepository;

    protected $config;

    /**
     * @var Session
     */
    private $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        CartHelper $cartHelper,
        OrderRepositoryInterface $orderRepository,
        BaseConfig $config,
        Session $checkoutSession
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
        $this->cartHelper = $cartHelper;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->config = $config;
    }

    public function execute()
    {
        $logger = $this->_objectManager->get('TamaraCheckoutLogger');
        try {
            $orderId = $this->_request->getParam('order_id', 0);
            $successStatus = $this->config->getCheckoutSuccessStatus();
            $order = $this->orderRepository->get($orderId);
            $order->setState($successStatus)->setStatus($successStatus);
            $this->orderRepository->save($order);

        } catch (\Exception $e) {
            $logger->debug(['Success has error' => $e->getMessage()]);
        }

        $page = $this->_pageFactory->create();

        $block = $page->getLayout()->getBlock('tamara_success');
        $block->setData('order_id', $orderId);

        $quoteId = $this->checkoutSession->getQuoteId();

        if ($quoteId === null) {
            return $page;
        }

        $this->cartHelper->removeCartAfterSuccess($quoteId);

        return $page;
    }
}