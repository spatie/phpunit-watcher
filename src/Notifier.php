<?php

namespace Spatie\PhpUnitWatcher;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;

class Notifier
{
    public static function testsPassed()
    {
        $notification = (new Notification)
            ->setTitle('PHPUnit Watcher')
            ->setBody('✅ Tests passed!');

        self::send($notification);
    }

    public static function testsFailed()
    {
        $notification = (new Notification)
            ->setTitle('PHPUnit Watcher')
            ->setBody('❗Tests failed!');

        self::send($notification);
    }

    private static function send(Notification $notification)
    {
        return NotifierFactory::create()->send($notification);
    }
}