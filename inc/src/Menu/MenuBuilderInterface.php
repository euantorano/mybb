<?php

namespace MyBB\Menu;

/**
 * Base interface for a menu builder.
 *
 * Instances implementing this interface can be registered with the @see \MyBB\Menu\MenuManager.
 */
interface MenuBuilderInterface
{
    /**
     * Build an instance of the menu.
     *
     * @return MenuInterface The created menu.
     */
    public function buildMenu(): MenuInterface;
}
