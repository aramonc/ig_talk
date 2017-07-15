<?php

use Arc\IgTalk\User;
use MongoDB\BSON\ObjectID;
use MongoDB\Client;

require_once __DIR__ . "/../vendor/autoload.php";

$logger = new \Monolog\Logger('log');
$logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());

$faker = \Faker\Factory::create();

$user = new \Arc\IgTalk\User();

$startTime = microtime(true);
$baseMemory = memory_get_usage();

$logger->addInfo('Starts with', ['time' => $startTime, 'memory' => \Arc\IgTalk\toHumanReadable($baseMemory)]);

$users = [];

$collection = (new Client("mongodb://datastore:27017"))->igtalk->users;

for ($i = 0; $i < 1000000; $i++) {
    $user = new User();
    $user->_id         = new ObjectID();
    $user->email       = $faker->email;
    $user->password    = $faker->password(12, 32);
    $user->firstName   = $faker->firstName();
    $user->lastName    = $faker->lastName;
    $user->phoneNumber = $faker->phoneNumber;

    $users[] = $user;

    if ($i % 5000 === 0) {
        $collection->insertMany($users);
        $iterationMemory = memory_get_usage() - $baseMemory;
        $logger->addInfo('Iteration ', ['index' => $i, 'memory' => \Arc\IgTalk\toHumanReadable($iterationMemory)]);
        $users = [];
    }
}

$spentTime = microtime(true) - $startTime;
$usedMemory = memory_get_usage() - $baseMemory;

$logger->addInfo('Ended with', ['time' => $spentTime, 'memory' => \Arc\IgTalk\toHumanReadable($usedMemory)]);
