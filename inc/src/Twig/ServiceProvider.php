<?php

namespace MyBB\Twig;

use Illuminate\Contracts\Container\Container;
use MyBB\Menu\MenuManager;
use MyBB\Twig\Extensions\CoreExtension;
use MyBB\Twig\Extensions\LangExtension;
use MyBB\Twig\Extensions\MenuExtension;
use MyBB\Twig\Extensions\ThemeExtension;
use MyBB\Twig\Extensions\UrlExtension;
use MyBB\Utilities\BreadcrumbManager;
use Psr\Container\ContainerInterface;

/** @property \MyBB\Application $app */
class ServiceProvider extends \MyBB\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->bind(CoreExtension::class, function (ContainerInterface $container) {
            return new CoreExtension(
                $container->get(\MyBB::class),
                $container->get(\MyLanguage::class),
                $container->get(\pluginSystem::class),
                $container->get(BreadcrumbManager::class)
            );
        });

        $this->app->bind(ThemeExtension::class, function (ContainerInterface $container) {
            return new ThemeExtension(
                $container->get(\MyBB::class),
                $container->get(\DB_Base::class)
            ) ;
        });

        $this->app->bind(LangExtension::class, function (ContainerInterface $container) {
            return new LangExtension(
                $container->get(\MyLanguage::class)
            );
        });

        $this->app->bind(UrlExtension::class, function () {
            return new UrlExtension();
        });

        $this->app->bind(MenuExtension::class, function (ContainerInterface $container) {
            return new MenuExtension(
                $container->get(MenuManager::class)
            );
        });

        $this->app->bind(\Twig_LoaderInterface::class, function () {
            if (defined('IN_ADMINCP')) {
                $paths = [
                    __DIR__ . '/../../views/admin/base',
                ];
            } else {
                // TODO: views for the current theme, it's parent, it's parent's parent, etc. should be here
                // The filesystem loader works by using files from the first array entry.
                // If a file doesn't exist, it looks in the second array entry and so on.
                // This allows us to easily implement template inheritance.

                $paths = [
                    __DIR__ . '/../../views/base',
                ];
            }

            // TODO: These paths should come from the theme system once it is written

            return new \Twig_Loader_Filesystem($paths);
        });

        $this->app->bind('twig.options', function () {
            return [
                'debug' => true, // TODO: In live environments this should be false
                'cache' => __DIR__ . '/../../../cache/views',
            ];
        });

        $this->app->bind(\Twig_Environment::class, function (Container $container) {
            $env = new \Twig_Environment(
                $container->make(\Twig_LoaderInterface::class),
                $container->make('twig.options')
            );

            $env->addExtension($container->make(CoreExtension::class));
            $env->addExtension($container->make(ThemeExtension::class));
            $env->addExtension($container->make(LangExtension::class));
            $env->addExtension($container->make(UrlExtension::class));
            $env->addExtension($container->make(MenuExtension::class));

            // TODO: this shouldn't be registered in live environments
            $env->addExtension(new \Twig_Extension_Debug());

            return $env;
        });
    }

    public function provides()
    {
        return [
            CoreExtension::class,
            ThemeExtension::class,
            LangExtension::class,
            UrlExtension::class,
            \Twig_LoaderInterface::class,
        ];
    }
}
