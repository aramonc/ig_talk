<?php

namespace Arc\IgTalk;

class Service
{
    const LIMIT = 100000;

    /**
     * @var Provider
     */
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function getList(int $limit): \Generator
    {
        $pages = ceil($limit / self::LIMIT);
        $innerLimit = $pages > 1 ? self::LIMIT : $limit;

        for ($page = 0; $page < $pages; $page++) {
            foreach ($this->provider->getPagedList($page, $innerLimit) as $user) {
                yield $user;
            }
        }

    }
}