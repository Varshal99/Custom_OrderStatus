<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Test\Integration\Controller\Adminhtml\Log;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Custom\OrderStatus\Model\ResourceModel\StatusLog\CollectionFactory;

class MassDeleteTest extends AbstractBackendController
{
    /**
     * @var string
     */
    protected $uri = 'backend/custom_order_status/log/massDelete';
    
    /**
     * @var string
     */
    protected $resource = 'Custom_OrderStatus::order_status_log';

    /**
     * @magentoDataFixture ../../../../_files/status_log_multiple_fixture.php
     */
    public function testExecute()
    {
        /** @var CollectionFactory $collectionFactory */
        $collectionFactory = $this->_objectManager->get(CollectionFactory::class);
        $collection = $collectionFactory->create();
        $idsToDelete = $collection->getAllIds();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'selected' => $idsToDelete,
            'namespace' => 'custom_order_status_log_listing',
        ]);

        $this->dispatch($this->uri);

        // Assert the user is redirected
        $this->assertRedirect($this->stringContains('custom_order_status/log/index'));

        // Assert the success message
        $this->assertSessionMessages(
            $this->equalTo([(string)__('%1 record(s) have been deleted.', count($idsToDelete))]),
            MessageInterface::TYPE_SUCCESS
        );

        // Assert the records are deleted from the database
        $collectionAfterDelete = $collectionFactory->create();
        $this->assertCount(0, $collectionAfterDelete);
    }
}
