<?php

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Custom\OrderStatus\Model\StatusLogFactory;
use Custom\OrderStatus\Api\StatusLogRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

/** @var StatusLogFactory $statusLogFactory */
$statusLogFactory = $objectManager->get(StatusLogFactory::class);
/** @var StatusLogRepositoryInterface $repository */
$repository = $objectManager->get(StatusLogRepositoryInterface::class);

for ($i = 1; $i <= 3; $i++) {
    $log = $statusLogFactory->create();
    $log->setOrderId($i);
    $log->setIncrementId('10000000' . $i);
    $log->setOldStatus('pending');
    $log->setNewStatus('complete');
    $repository->save($log);
}
