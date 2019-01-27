<?php

namespace PL\Providers;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use PL\Providers\DataProvider;

class CacheProvider implements ProviderInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DataProvider
     */
    protected $provider;

    const CACHE_PREFIX = 'provider.';

    const CACHE_TIME   = '+1 day';

    /**
     * CacheProvider constructor.
     *
     * @param DataProvider           $provider
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(DataProvider $provider, CacheItemPoolInterface $cache)
    {
        $this->provider = $provider;
        $this->cache    = $cache;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $input) : array
    {
        try {
            $cacheKey  = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->provider->get($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify(self::CACHE_TIME)
                );

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error');
            throw new $e; // todo make custom Exception
        }
    }

    public function getCacheKey(array $input) : string
    {
        return self::CACHE_PREFIX . sha1(json_encode($input));
    }
}
