<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Custom\OrderStatus\Api\StatusLogRepositoryInterface;
use Custom\OrderStatus\Model\StatusLogFactory;
use Psr\Log\LoggerInterface;

class LogOrderStatusChange implements ObserverInterface
{
    /**
     * @var StatusLogRepositoryInterface
     */
    protected $statusLogRepository;

    /**
     * @var StatusLogFactory
     */
    protected $statusLogFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        StatusLogRepositoryInterface $statusLogRepository,
        StatusLogFactory $statusLogFactory,
        LoggerInterface $logger
    ) {
        $this->statusLogRepository = $statusLogRepository;
        $this->statusLogFactory = $statusLogFactory;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $oldStatus = $order->getOrigData('status');
        $newStatus = $order->getStatus();

        if ($newStatus && $oldStatus !== $newStatus) {
            try {
                /** @var \Custom\OrderStatus\Model\StatusLog $logEntry */
                $logEntry = $this->statusLogFactory->create();
                $logEntry->setData([
                    'order_id' => $order->getId(),
                    'increment_id' => $order->getIncrementId(),
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ]);

                $this->statusLogRepository->save($logEntry);

            } catch (\Exception $e) {
                $this->logger->error('Error logging order status change: ' . $e->getMessage());
            }
        }
    }
}
