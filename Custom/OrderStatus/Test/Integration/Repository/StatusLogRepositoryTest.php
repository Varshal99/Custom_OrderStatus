<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Test\Integration\Repository;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Custom\OrderStatus\Api\StatusLogRepositoryInterface;

class StatusLogRepositoryTest extends TestCase
{
    /**
     * @var StatusLogRepositoryInterface
     */
    private $repository;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->repository = $objectManager->get(StatusLogRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../_files/status_log_fixture.php
     */
    public function testSaveAndGet()
    {
        // The fixture has already saved a log. We'll find it.
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('increment_id', '100000001')->create();
        
        $result = $this->repository->getList($searchCriteria);
        
        $this->assertEquals(1, $result->getTotalCount());
        
        $items = $result->getItems();
        $log = array_pop($items);

        $this->assertEquals('100000001', $log->getIncrementId());
        $this->assertEquals('pending', $log->getOldStatus());
        $this->assertEquals('processing', $log->getNewStatus());
    }
}
