<?php
namespace Piko\Tests\mock;

class WrongAssets extends \Piko\AssetBundle
{
    public $name = 'wrong';

    // Intentionally wrong path
    public $sourcePath =  __DIR__ . '/assetics';

    public $js = [
        'wrong.js',
    ];
}
