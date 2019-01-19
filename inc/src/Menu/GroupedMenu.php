<?php

namespace MyBB\Menu;

/**
 * A menu that is split into groups.
 */
class GroupedMenu implements MenuInterface, \IteratorAggregate, \ArrayAccess, \Countable
{
    const DEFAULT_TEMPLATE = 'menu/grouped.twig';

    /**
     * @var \pluginSystem $pluginSystem
     */
    protected $pluginSystem;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $templateName
     */
    protected $templateName;

    /**
     * An array of menu items.
     *
     * @var array $items
     */
    protected $items;

    /**
     * GroupedMenu constructor.
     *
     * @param \pluginSystem $pluginSystem Plugin system.
     * @param string $name The name of the menu. This is used to build the hook names when rendering the menu.
     * @param string $templateName The name of the template to use to render the menu.
     */
    public function __construct(
        \pluginSystem $pluginSystem,
        string $name,
        string $templateName = GroupedMenu::DEFAULT_TEMPLATE
    ) {
        $this->pluginSystem = $pluginSystem;
        $this->name = $name;
        $this->templateName = $templateName;

        $this->items = [];
    }

    /**
     * Get the name of the menu.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the menu.
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the template to use to render the menu.
     *
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * Set the template to use to render the menu.
     *
     * @param string $templateName
     */
    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    /**
     * Add a new group to the menu.
     *
     * If the item already exists, its existing attributes will be updated with the new attributes.
     *
     * @param string $key The key to use for the group.
     * @param array $attributes Any extra attributes to include with the group.
     *
     * @return \MyBB\Menu\GroupedMenu
     */
    public function addGroup(string $key, array $attributes = []): GroupedMenu
    {
        if (!isset($this->items[$key])) {
            if (!isset($attributes['children'])) {
                $attributes['children'] = [];
            }

            $this->items[$key] = $attributes;
        } else {
            $attributes = array_merge($this->items[$key], $attributes);

            $this->items[$key] = $attributes;
        }

        return $this;
    }

    /**
     * Remove a group from the menu if it exists.
     *
     * @param string $key The key used for the group.
     *
     * @return \MyBB\Menu\GroupedMenu
     */
    public function removeGroup(string $key): GroupedMenu
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }

        return $this;
    }

    /**
     * Check whether a group exists in the menu.
     *
     * @param string $key The key used for the group.
     *
     * @return bool
     */
    public function containsGroup(string $key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Add a new menu item to the given group.
     *
     * @param string $group The key of the group to add the item to. If the group doesn't exist, it will be created.
     * @param string $key The key for the item.
     * @param array $attributes Any extra attributes to include with the item.
     *
     * @return \MyBB\Menu\GroupedMenu
     */
    public function addItemToGroup(string $group, string $key, array $attributes = []): GroupedMenu
    {
        if (!$this->containsGroup($group)) {
            $this->addGroup($group);
        }

        if (!isset($this->items[$group]['children'][$key])) {
            $this->items[$group]['children'][$key] = $attributes;
        } else {
            $attributes = array_merge($this->items[$group]['children'][$key], $attributes);

            $this->items[$group]['children'][$key] = $attributes;
        }

        return $this;
    }

    /**
     * Remove an item from the given group.
     *
     * @param string $group The key of the group to remove the item from.
     * @param string $key The key for the item.
     *
     * @return \MyBB\Menu\GroupedMenu
     */
    public function removeItemFromGroup(string $group, string $key): GroupedMenu
    {
        if (isset($this->items[$group]['children'][$key])) {
            unset($this->items[$group]['children'][$key]);
        }

        return $this;
    }

    /**
     * Check whether an item exists within a group in the menu.
     *
     * @param string $group The key of the group to check.
     * @param string $key The key of the item.
     *
     * @return bool
     */
    public function groupContainsItem(string $group, string $key): bool
    {
        return isset($this->items[$group]['children'][$key]);
    }

    /**
     * Render the menu to a string using the given twig environment.
     *
     * @param \Twig_Environment $twig The Twig environment to use to render the menu.
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(\Twig_Environment $twig): string
    {
        $hookArgs = [
            'twig' => $twig,
            'menu' => $this,
        ];

        $this->pluginSystem->run_hooks("menu_{$this->name}_before_render", $hookArgs);

        $template = $twig->render($this->templateName, [
            'menu' => $this,
        ]);

        $hookArgs['template'] = &$template;

        $this->pluginSystem->run_hooks("menu_{$this->name}_after_render", $hookArgs);

        return $template;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->items);
    }
}
