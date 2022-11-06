<?php

/**
 * This file is part of Piko framework
 *
 * @copyright 2019-2022 Sylvain PHILIP
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/asset-bundle
 */

declare(strict_types=1);

namespace Piko;

use Piko\AssetBundle\Event\BeforeRegisterEvent;

/**
 * AssetBundle represents a collection of CSS files and JS files to publish inside the public path.
 *
 * @author Sylvain PHILIP <contact@sphilip.com>
 */
abstract class AssetBundle
{
    use EventHandlerTrait;

    /**
     * The bundle name. (eg. jquery, bootstrap, etc.)
     *
     * @var string
     */
    public $name = '';

    /**
     * The directory that contains the source asset files for this asset bundle.
     * You can use either a directory or an alias of the directory.
     *
     * @var string
     */
    public $sourcePath = '';

    /**
     * List of JavaScript files that this bundle contains.
     *
     * @var string[]
     */
    public $js = [];

    /**
     * List of CSS files that this bundle contains.
     *
     * @var string[]
     */
    public $css = [];

    /**
     * Position of the js file in the generated view.
     *
     * @var integer
     * @see View
     */
    public $jsPosition = View::POS_END;

    /**
    * The root directory storing the published asset files.
    *
    * @var string
    */
    public $publishedBasePath = '@webroot/assets';

    /**
     * The base URL through which the published asset files can be accessed.
     *
     * @var string
     */
    public $publishedBaseUrl = '@web/assets';

    /**
     * Bundle dependencies.
     *
     * @var string[]
     */
    public $dependencies = [];

    /**
     * list of the registered asset bundles. The keys are the bundle names
     * and the values are the AssetBundle class name.
     *
     * @var AssetBundle[]
     * @see AssetBundle::register()
     */
    protected static $assetBundles = [];


    /**
     * Registers this asset bundle with a view.
     *
     * @param View $view the view to be registered with
     * @return AssetBundle the registered asset bundle instance
     */
    public static function register(View $view): AssetBundle
    {
        $className = \get_called_class();

        // https://github.com/phpstan/phpstan/issues/4061
        $bundle = new $className(); // @phpstan-ignore-line

        if (isset(static::$assetBundles[$className])) {
            return static::$assetBundles[$className];
        }

        $event = $bundle->trigger(new BeforeRegisterEvent($bundle));

        assert($event instanceof BeforeRegisterEvent);

        static::$assetBundles[\get_class($event->bundle)] = $event->bundle;

        foreach ($bundle->dependencies as $class) {
            \call_user_func($class . '::register', $view); // @phpstan-ignore-line
        }

        $bundle->publish();

        $getFileUrl = function ($file) use ($bundle) {
            if (preg_match('/^http:/', $file)) {
                return $file;
            }

            return \Piko::getAlias($bundle->publishedBaseUrl) . '/' . $bundle->name . '/' . $file;
        };

        foreach ($bundle->css as $cssFile) {
            $view->registerCSSFile($getFileUrl($cssFile));
        }

        foreach ($bundle->js as $jsFile) {
            $view->registerJsFile($getFileUrl($jsFile), $bundle->jsPosition);
        }

        return $bundle;
    }

    /**
     * Publish assets into public path
     *
     * @return void
     */
    public function publish(): void
    {
        if (!empty($this->sourcePath) && !file_exists(\Piko::getAlias($this->publishedBasePath) . '/' . $this->name)) {
            $this->copy(
                (string) \Piko::getAlias($this->sourcePath),
                (string) \Piko::getAlias($this->publishedBasePath) . '/' . $this->name
            );
        }
    }

    /**
     * Copy recursively a folder into another one.
     *
     * @param string $src The source directory to copy
     * @param string $dest The destination directory to copy
     * @return void
     * @throws \RuntimeException if src not exists
     */
    protected function copy($src, $dest)
    {
        if (!\file_exists($src)) {
            throw new \RuntimeException(sprintf('Src: %s does not exists.', $src));
        }

        $dir = \opendir($src);

        if (\is_resource($dir)) {
            \mkdir($dest, 0755, true);

            while (false !== ($file = \readdir($dir))) {

                if (( $file != '.' ) && ( $file != '..' )) {
                    if (\is_dir($src . '/' . $file)) {
                        $this->copy($src . '/' . $file, $dest . '/' . $file);
                    } else {
                        \copy($src . '/' . $file, $dest . '/' . $file);
                    }
                }
            }

            \closedir($dir);
        }
    }
}
