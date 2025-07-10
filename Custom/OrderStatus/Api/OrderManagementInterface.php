<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Api;

interface OrderManagementInterface
{
    /**
     * Update the order status.
     *
     * @param string $incrementId The order increment ID.
     * @param string $status The new order status.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateOrderStatus($incrementId, $status);
}
