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
$emailLogPath = __DIR__ . '/../data/user.txt';
$emailLog = fopen($emailLogPath, 'w+');

$logger->addInfo(
    'Pre-process:',
    ['time' => $startTime, 'memory' => toHumanReadable($baseMemory)]
);

for ($up = 0; $up < 100; $up++) {
    $uc = 0;
    foreach ($provider->getPagedList($up, 10000) as $user) {
        /** @var $user User */
        fwrite($emailLog, $user->email . "\n");

        if ($uc % 500 === 0) {
            $consumedM = memory_get_usage() - $baseMemory;
            $consumedT = microtime(true) - $startTime;

            $logger->addInfo(
                'So far: ',
                ['time' => $consumedT, 'memory' => toHumanReadable($consumedM)]
            );
        }

        $uc++;
    }
}

fclose($emailLog);
unlink($emailLogPath);