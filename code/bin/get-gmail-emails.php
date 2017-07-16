<?php

use Arc\IgTalk\Provider;
use Arc\IgTalk\User;
use MongoDB\Client;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use function \Arc\IgTalk\toHumanReadable;

require_once __DIR__ . "/../vendor/autoload.php";

$logger = new Logger('log');
$logger->pushHandler(new ErrorLogHandler());

$startTime = microtime(true);
$baseMemory = memory_get_usage();

$logger->addInfo(
    'Starts with',
    ['time' => $startTime, 'memory' => toHumanReadable($baseMemory)]
);

$provider = new Provider(new Client("mongodb://datastore:27017"));
$service = new \Arc\IgTalk\Service($provider);

$emailLogPath = __DIR__ . '/../data/user.txt';
$emailLog = fopen($emailLogPath, 'w+');

$logger->addInfo(
    'Pre-process:',
    ['time' => $startTime, 'memory' => toHumanReadable($baseMemory)]
);

function filterGmailUsers(User $user): bool
{
    $emailParts = explode('@', $user->email);

    return $emailParts[1] === 'gmail.com';
}

$filtered = new CallbackFilterIterator(
    $service->getList(1000000),
    'filterGmailUsers'
);

$count = 0;
foreach ($filtered as $user) {
    fwrite($emailLog, $user->email . "\n");
    $count++;
}

fclose($emailLog);

$logger->addInfo(sprintf('Found %d users with gmail addresses.', $count));
$logger->addInfo(sprintf('Took %f seconds to complete', microtime(true) - $startTime));