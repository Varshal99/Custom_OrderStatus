<?php

declare(strict_types=1);

namespace Custom\OrderStatus\Plugin\Model;

use Custom\OrderStatus\Api\OrderManagementInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Plugin to add rate limiting to the OrderManagement API.
 */
class OrderManagementRateLimiter
{
    /**
     * Number of allowed requests.
     */
    private const REQUEST_LIMIT = 20;

    /**
     * Time period in seconds.
     */
    private const TIME_PERIOD = 60;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var FrontendInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param RequestInterface $request
     * @param FrontendPool $cachePool
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        FrontendPool $cachePool,
        LoggerInterface $logger
    ) {
        $this->request = $request;
        $this->cache = $cachePool->get('default');
        $this->logger = $logger;
    }

    /**
     * Before plugin for updateOrderStatus.
     *
     * @param OrderManagementInterface $subject
     * @return void
     * @throws LocalizedException
     */
    public function beforeUpdateOrderStatus(OrderManagementInterface $subject): void
    {
        $clientIp = $this->request->getClientIp();
        if (!$clientIp) {
            return;
        }

        $cacheKey = 'rate_limit_order_status_' . str_replace('.', '_', $clientIp);
        $requestCount = (int)$this->cache->load($cacheKey);

        if ($requestCount >= self::REQUEST_LIMIT) {
            $this->logger->warning('Rate limit exceeded for order status update API.', ['ip' => $clientIp]);
            throw new LocalizedException(
                __('Too Many Requests. Please try again later.'),
                null,
                429
            );
        }

        $requestCount++;
        $this->cache->save((string)$requestCount, $cacheKey, [], self::TIME_PERIOD);
    }
}
