<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Model\ResourceModel\StatusLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = 'log_id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Custom\OrderStatus\Model\StatusLog::class,
            \Custom\OrderStatus\Model\ResourceModel\StatusLog::class
        );
    }
}
