<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Custom\OrderStatus\Model\ResourceModel\StatusLog\CollectionFactory;
use Custom\OrderStatus\Model\ResourceModel\StatusLog\Collection;

/**
 * Class StatusLogDataProvider
 *
 * Data provider for the Order Status Log UI component grid.
 */
class StatusLogDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * StatusLogDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    /**
     * Retrieves data to be displayed in the UI grid.
     *
     * @return array
     */
    public function getData(): array
    {
        $this->collection->load();

        $items = [];
        foreach ($this->collection as $item) {
            $items[] = $item->getData();
        }

        return [
            'totalRecords' => $this->collection->getSize(),
            'items' => $items,
        ];
    }
}
