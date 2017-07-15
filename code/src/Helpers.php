<?php

namespace Arc\IgTalk;

function toHumanReadable(int $memory): string
{
    $sizeType = 0;

    while ($memory > 1024) {
        $memory = $memory / 1024;
        $sizeType++;
    }

    switch ($sizeType) {
        case 0:
            $size = 'B';
            break;
        case 1:
            $size = 'KB';
            break;
        case 2:
            $size = 'MB';
            break;
        case 3:
            $size = 'GB';
            break;
        default:
            $size = '+++';
    }

    return sprintf('%f %s', $memory, $size);
}