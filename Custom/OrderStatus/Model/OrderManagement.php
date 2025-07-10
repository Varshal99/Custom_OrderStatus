<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Model;

use Custom\OrderStatus\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\OrderFactory;

class OrderManagement implements OrderManagementInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOrderStatus($incrementId, $status)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($incrementId);

        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order with increment ID "%1" not found.', $incrementId));
        }

        try {
            $order->setStatus($status);
            $order->addStatusToHistory($status, 'Order status updated via custom API.', true);
            $this->orderRepository->save($order);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Could not update the order status. Please try again.'));
        }

        return true;
    }
}
