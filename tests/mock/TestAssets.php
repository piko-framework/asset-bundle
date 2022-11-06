<?php
namespace Piko\Tests\mock;

use Piko\AssetBundle\Event\BeforeRegisterEvent;

class TestAssets extends \Piko\AssetBundle
{
    public $name = 'test';

    public $sourcePath =  __DIR__ . '/assets';

    public $js = [
        'test.js',
    ];

    public $css = [
        'css/test.css',
    ];

    public $dependencies = [
        TestParentAssets::class
    ];

    public $registrationCount = 0;

    public function __construct()
    {
        $this->on(BeforeRegisterEvent::class, function(BeforeRegisterEvent $event) {
            if ($event->bundle instanceof TestAssets) {
                $event->bundle->registrationCount++;
            }
        });
    }
}
