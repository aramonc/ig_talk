<?php

namespace Arc\IgTalk;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

class Provider
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Client $mongoClient)
    {
        $this->collection = $mongoClient->selectDatabase('igtalk')
                                        ->selectCollection('users');
    }

    public function getPagedList(int $page = 0, int $limit = 100): \Generator
    {
        $cursor = $this->collection->find(
            [],
            [
                'limit' => $limit,
                'skip' => $page * $limit,
            ]
        );

        foreach ($cursor as $data) {
            /** @var $data BSONDocument */
            yield $this->createUserEntity($data->getArrayCopy());
        }
    }

    protected function createUserEntity(array $data): User
    {
        $user = new User();
        $user->_id = $data['_id'];
        $user->firstName = $data['firstName'];
        $user->lastName = $data['lastName'];
        $user->phoneNumber = $data['phoneNumber'];
        $user->password = $data['password'];
        $user->email = $data['email'];

        return $user;
    }
}