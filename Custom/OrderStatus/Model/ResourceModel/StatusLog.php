<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StatusLog extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('custom_order_status_log', 'log_id');
    }
}
