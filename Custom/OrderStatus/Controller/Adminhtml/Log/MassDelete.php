<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Controller\Adminhtml\Log;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Custom\OrderStatus\Model\ResourceModel\StatusLog\CollectionFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class MassDelete
 *
 * Controller for mass deleting order status log records from the admin grid.
 */
class MassDelete extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Custom_OrderStatus::order_status_log';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute mass delete action.
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;

        foreach ($collection as $item) {
            $item->delete();
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('%1 record(s) have been deleted.', $count));
        return $this->_redirect('*/*/index');
    }
}
