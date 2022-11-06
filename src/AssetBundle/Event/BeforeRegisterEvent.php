<?php

/**
 * This file is part of Piko Framework
 *
 * @copyright 2019-2022 Sylvain Philip
 * @license LGPL-3.0-or-later; see LICENSE.txt
 * @link https://github.com/piko-framework/asset-bundle
 */

declare(strict_types=1);

namespace Piko\AssetBundle\Event;

use Piko\Event;
use Piko\AssetBundle;

/**
 * Event emmited before bundle registration
 *
 * @author Sylvain Philip <contact@sphilip.com>
 */
class BeforeRegisterEvent extends Event
{
    /**
     * The registered bundle
     *
     * @var AssetBundle
     */
    public $bundle;

    /**
     * @param AssetBundle $bundle
     */
    public function __construct(AssetBundle $bundle)
    {
        $this->bundle = $bundle;
    }
}
