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

$log = $statusLogFactory->create();
$log->setOrderId(1);
$log->setIncrementId('100000001');
$log->setOldStatus('pending');
$log->setNewStatus('processing');

$repository->save($log);
