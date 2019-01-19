<?php

namespace MyBB\Menu;

use MyBB\Menu\Builders\UserCpMenuBuilder;
use Psr\Container\ContainerInterface;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Registers bindings in the container.
     */
    public function register()
    {
        $this->app->singleton(MenuManager::class, function (ContainerInterface $container) {
            $menuManager = new MenuManager($container);

            $menuManager->addMenuBuilder('user_cp', $container->get('user_cp_menu_builder'));

            return $menuManager;
        });

        $this->app->singleton('user_cp_menu_builder', function (ContainerInterface $container) {
            return function (ContainerInterface $cont) {
                return new UserCpMenuBuilder(
                    $cont->get(\MyBB::class),
                    $cont->get(\MyLanguage::class),
                    $cont->get(\pluginSystem::class)
                );
            };
        });
    }

    public function provides()
    {
        return [
            MenuManager::class,
        ];
    }
}
