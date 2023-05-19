<?php

namespace App\Http\Helpers;

class ServerEvent
{
    public static function send($event, $data)
    {
        echo "event: {$event}\n";
        echo 'data: ' . $data;
        echo "\n\n";
        ob_flush();
        flush();
    }
}
