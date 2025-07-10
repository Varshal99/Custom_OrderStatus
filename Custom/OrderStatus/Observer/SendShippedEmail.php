<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SendShippedEmail implements ObserverInterface
{
    protected $transportBuilder;
    protected $storeManager;
    protected $logger;

    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();

        if ($shipment->getEmailSent()) {
            return;
        }

        try {
            $store = $this->storeManager->getStore($order->getStoreId());

            $subject = __('Your order %1 has been shipped!', $order->getIncrementId());

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('custom_order_shipped_notification')
                ->setTemplateOptions(['area' => 'frontend', 'store' => $order->getStoreId()])
                ->setTemplateVars([
                    'order_increment_id' => $order->getIncrementId(),
                    'customer_name' => $order->getCustomerName(),
                ])
                ->setFromByScope('general', $store->getId())
                ->addTo($order->getCustomerEmail(), $order->getCustomerName())
                ->getTransport();

            $transport->getMessage()->setSubject($subject);

            $transport->sendMessage();
            
            $shipment->setEmailSent(true);

        } catch (\Exception $e) {
            $this->logger->error('Error sending shipment notification email: ' . $e->getMessage());
        }
    }
}
