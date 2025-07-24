<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SendShippedEmail
 *
 * Observer to send a custom shipment notification email when an order is shipped.
 */
class SendShippedEmail implements ObserverInterface
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SendShippedEmail constructor.
     *
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Execute observer method to send a custom shipped email notification.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();

        // Avoid resending the email if it was already sent
        if ($shipment->getEmailSent()) {
            return;
        }

        try {
            $store = $this->storeManager->getStore($order->getStoreId());

            $subject = __('Your order %1 has been shipped!', $order->getIncrementId());

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('custom_order_shipped_notification') // Email template identifier
                ->setTemplateOptions([
                    'area' => 'frontend',
                    'store' => $order->getStoreId(),
                ])
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
