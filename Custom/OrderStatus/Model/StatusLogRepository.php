<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Model;

use Custom\OrderStatus\Api\StatusLogRepositoryInterface;
use Custom\OrderStatus\Model\ResourceModel\StatusLog as StatusLogResource;
use Magento\Framework\Exception\CouldNotSaveException;

class StatusLogRepository implements StatusLogRepositoryInterface
{
    /**
     * @var StatusLogResource
     */
    protected $resource;

    /**
     * @param StatusLogResource $resource
     */
    public function __construct(StatusLogResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(StatusLog $statusLog)
    {
        try {
            $this->resource->save($statusLog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the status log: %1', $exception->getMessage()));
        }
        return $statusLog;
    }
}
