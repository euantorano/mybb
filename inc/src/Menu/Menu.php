<?php

namespace MyBB\Menu;

use Traversable;

/**
 * A standard menu, that contains items in a specific order.
 */
class Menu implements MenuInterface, \Countable, \IteratorAggregate
{
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
     * These are keyed by the item's name, and the value contains at least a 'name' and 'order' field.
     *
     * @var array $items
     */
    protected $items;

    public function __construct(\pluginSystem $pluginSystem, string $name, string $templateName)
    {
        $this->pluginSystem = $pluginSystem;
        $this->name = $name;
        $this->templateName = $templateName;

        $this->items = [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * @param string $templateName
     */
    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    /**
     * @return array
     */
    public function &getRawItems(): array
    {
        return $this->items;
    }

    /**
     * Get the highest position of items in the menu.
     *
     * @return int The highest position, or -1 if the menu is empty.
     */
    public function getHighestPosition(): int
    {
        $max = -1;

        foreach ($this->items as $item) {
            if ($item['order'] > $max) {
                $max = $item['order'];
            }
        }

        return $max;
    }

    /**
     * Add an item to the menu.
     *
     * If an item with the same name already exists, it will be overwritten.
     *
     * @param string $name The name of the item to add.
     * @param int|null $order The position of the item in the menu.
     * @param array $attributes An array of extra attributes for the item.
     */
    public function addItem(string $name, ?int $order = null, array $attributes = [])
    {
        if (is_null($order)) {
            $order = $this->getHighestPosition() + 1;
        }

        $attributes['name'] = $name;
        $attributes['order'] = $order;

        $this->items[$name] = $attributes;
    }

    /**
     * Check if the menu contains an item with the given name.
     *
     * @param string $name The name of the item to check.
     *
     * @return bool
     */
    public function containsItem(string $name): bool
    {
        return isset($this->items[$name]);
    }

    /**
     * Remove an item from the menu.
     *
     * @param string $name The name of the item to remove.
     *
     * @return bool Whether the item was in the menu before being removed.
     */
    public function removeItem(string $name): bool
    {
        if (isset($this->items[$name])) {
            unset($this->items[$name]);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(\Twig_Environment $twig): string
    {
        $templateContext = [
            'menu' => $this,
        ];

        $hookArgs = [
            'twig' => $twig,
            'menu' => $this,
            'template_context' => &$templateContext,
        ];

        $this->pluginSystem->run_hooks("menu_{$this->name}_before_render", $hookArgs);

        $template = $twig->render($this->templateName, $templateContext);

        $hookArgs['template'] = &$template;

        $this->pluginSystem->run_hooks("menu_{$this->name}_after_render", $hookArgs);

        return $template;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $arrayIterator = new \ArrayIterator($this->items);

        $arrayIterator->uasort(function (array $a, array $b): int {
            if ($a['order'] < $b['order']) {
                return -1;
            }

            if ($a['order'] === $b['order']) {
                return 0;
            }

            return 1;
        });

        return $arrayIterator;
    }
}
