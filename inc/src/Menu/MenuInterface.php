<?php

namespace MyBB\Menu;

/**
 * Base interface for a menu.
 */
interface MenuInterface
{
    /**
     * Render the menu to a string using the given twig environment.
     *
     * @param \Twig_Environment $twig The Twig environment to use to render the menu.
     *
     * @return string The rednered menu as a string.
     */
    public function render(\Twig_Environment $twig): string;
}
