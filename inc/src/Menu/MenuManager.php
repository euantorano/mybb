<?php

namespace MyBB\Menu;

use Psr\Container\ContainerInterface;

/**
 * Menu manager to manage different menus throughout MyBB.
 *
 * This is usually accessed through the `MenuExtension` Twig extension.
 *
 * @see \MyBB\Twig\Extensions\MenuExtension
 */
class MenuManager
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var array $menuBuilders
     */
    protected $menuBuilders;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->menuBuilders = [];
    }

    /**
     * Register a menu builder with the manager.
     *
     * If a builder already exists with the given {@see $name}, it will be overwritten by the new builder.
     *
     * @param string $name The name for the menu builder.
     * @param callable|MenuBuilderInterface $builder The builder to register.
     * Callable builders will be passed the container instance when called.
     * Callable builders will only be called once and their result will be re-used.
     *
     * @throws \InvalidArgumentException Thrown if {@see $builder} is not a callable function
     * or an instance of {@see \MyBB\Menu\MenuBuilderInterface}.
     */
    public function addMenuBuilder(string $name, $builder): void
    {
        if (is_callable($builder)) {
            $this->menuBuilders[$name] = [
                'callback' => $builder,
            ];
        } elseif ($builder instanceof MenuBuilderInterface) {
            $this->menuBuilders[$name] = $builder;
        } else {
            throw new \InvalidArgumentException(
                '$builder must be either a callable function, or an object implementing MenuBuilderInterface'
            );
        }
    }

    /**
     * Check whether the given menu builder exists.
     *
     * @param string $name The name of the menu builder to check.
     *
     * @return bool
     */
    public function doesMenuBuilderExist(string $name): bool
    {
        return isset($this->menuBuilders[$name]);
    }

    /**
     * Get the menu builder with the given name if it exists.
     *
     * @param string $name The name of the menu builder to get.
     *
     * @return \MyBB\Menu\MenuBuilderInterface|null The menu builder if it is registered, or null otherwise.
     */
    public function getMenuBuilder(string $name): ?MenuBuilderInterface
    {
        if (isset($this->menuBuilders[$name])) {
            if (is_array($this->menuBuilders[$name])) {
                if (!isset($this->menuBuilders[$name]['resolved'])) {
                    $resolved = $this->menuBuilders[$name]['callback']($this->container);

                    if (is_null($resolved) || !($resolved instanceof MenuBuilderInterface)) {
                        if (!is_object($resolved)) {
                            $returnedType = gettype($resolved);
                        } else {
                            $returnedType = get_class($resolved);
                        }

                        throw new \LogicException(
                            "Callback builder for menu '{$name}' returned an invalid type: {$returnedType}"
                        );
                    }

                    $this->menuBuilders[$name]['resolved'] = $resolved;
                }

                return $this->menuBuilders[$name]['resolved'];
            }

            return $this->menuBuilders[$name];
        }

        return null;
    }

    /**
     * Get all of the registered menu builders.
     *
     * @return array An associative array of menu builders.
     */
    public function getMenuBuilders(): array
    {
        return $this->menuBuilders;
    }
}
