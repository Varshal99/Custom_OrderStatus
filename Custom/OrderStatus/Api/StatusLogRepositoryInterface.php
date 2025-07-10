<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Api;

use Custom\OrderStatus\Model\StatusLog;

interface StatusLogRepositoryInterface
{
    /**
     * Save order status log.
     *
     * @param StatusLog $statusLog
     * @return StatusLog
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(StatusLog $statusLog);
}
