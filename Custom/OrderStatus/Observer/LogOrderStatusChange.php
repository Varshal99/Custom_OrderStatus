<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Custom\OrderStatus\Api\StatusLogRepositoryInterface;
use Custom\OrderStatus\Model\StatusLogFactory;
use Psr\Log\LoggerInterface;

/**
 * Class LogOrderStatusChange
 *
 * Observer that logs order status changes to a custom log table.
 */
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

    /**
     * LogOrderStatusChange constructor.
     *
     * @param StatusLogRepositoryInterface $statusLogRepository
     * @param StatusLogFactory $statusLogFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        StatusLogRepositoryInterface $statusLogRepository,
        StatusLogFactory $statusLogFactory,
        LoggerInterface $logger
    ) {
        $this->statusLogRepository = $statusLogRepository;
        $this->statusLogFactory = $statusLogFactory;
        $this->logger = $logger;
    }

    /**
     * Execute observer to log status change when order status is updated.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
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
                    'order_id'     => $order->getId(),
                    'increment_id' => $order->getIncrementId(),
                    'old_status'   => $oldStatus,
                    'new_status'   => $newStatus,
                ]);

                $this->statusLogRepository->save($logEntry);

            } catch (\Exception $e) {
                $this->logger->error('Error logging order status change: ' . $e->getMessage());
            }
        }
    }
}
