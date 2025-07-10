<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Model;

use Magento\Framework\Model\AbstractModel;

class StatusLog extends AbstractModel
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(\Custom\OrderStatus\Model\ResourceModel\StatusLog::class);
    }
}
