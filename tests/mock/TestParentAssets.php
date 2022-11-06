<?php
namespace Piko\Tests\mock;

class TestParentAssets extends \Piko\AssetBundle
{
    public $name = 'test-parent';

    public $sourcePath =  __DIR__ . '/assets';

    public $js = [
        'parent.js',
        'http://domain.com/js/test.js'
    ];

    public $css = [
        'css/parent.css',
        'http://domain.com/css/test.css'
    ];
}
