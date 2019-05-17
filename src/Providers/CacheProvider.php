<?php

namespace PL\Providers;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class CacheProvider implements ProviderInterface, LoggerAwareInterface
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    const CACHE_PREFIX = 'provider.';

    /**
     * @var string
     */
    protected $cacheTime = '+1 day';

    /**
     * CacheProvider constructor.
     *
     * @param ProviderInterface $provider
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(ProviderInterface $provider, CacheItemPoolInterface $cache)
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
            $cacheItem = $this->cache->getItem($this->getCacheKey($input));

            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->provider->get($input);

            $cacheItem
                ->set($result)
                ->expiresAt(new DateTime($this->cacheTime));

            $this->cache->save($cacheItem);

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('CacheProvider: ' . $e->getMessage(), ['input' => $input]);
            throw new $e; // todo make custom Exception
        }
    }

    public function getCacheKey(array $input) : string
    {
        return self::CACHE_PREFIX . sha1(json_encode($input));
    }
}
