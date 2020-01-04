<?php

namespace Spatie\PhpUnitWatcher;

use Joli\JoliNotif\Notification as JoliNotification;
use Joli\JoliNotif\NotifierFactory;

class Notification
{
    /** @var \Joli\JoliNotif\Notification */
    protected $joliNotification;

    public static function create()
    {
        $joliNotification = (new JoliNotification)
            ->setTitle('PHPUnit Watcher')
            ->setIcon(__DIR__.'/../images/notificationIcon.png');

        return new static($joliNotification);
    }

    protected function __construct(JoliNotification $joliNotification)
    {
        $this->joliNotification = $joliNotification;
    }

    public function passingTests()
    {
        $this->joliNotification->setBody('✅ Tests passed!');

        $this->send();
    }

    public function failingTests()
    {
        $this->joliNotification->setBody('❌ Tests failed!');

        $this->send();
    }

    protected function send()
    {
        return NotifierFactory::create()->send($this->joliNotification);
    }
}
