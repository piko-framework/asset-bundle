<?php
namespace tests\mock;

class WrongAssets extends \piko\AssetBundle
{
    public $name = 'wrong';

    // Intentionally wrong path
    public $sourcePath =  __DIR__ . '/assetics';

    public $js = [
        'wrong.js',
    ];
}
