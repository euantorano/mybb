<?php

namespace MyBB\Menu\Builders;

use MyBB\Menu\GroupedMenu;
use MyBB\Menu\MenuBuilderInterface;
use MyBB\Menu\MenuInterface;

class UserCpMenuBuilder implements MenuBuilderInterface
{
    /**
     * @var \MyBB $mybb
     */
    protected $mybb;

    /**
     * @var \MyLanguage $lang
     */
    protected $lang;

    /**
     * @var \pluginSystem $plugins
     */
    protected $plugins;

    /**
     * A cached copy of the menu. This ensures the menu is only built once.
     *
     * @var MenuInterface|null $builtMenu
     */
    protected $builtMenu;

    public function __construct(\MyBB $mybb, \MyLanguage $lang, \pluginSystem $plugins)
    {
        $this->mybb = $mybb;
        $this->lang = $lang;
        $this->plugins = $plugins;

        $this->builtMenu = null;
    }

    /**
     * @inheritdoc
     */
    public function buildMenu(): MenuInterface
    {
        if (is_null($this->builtMenu)) {
            $this->builtMenu = new GroupedMenu($this->plugins, 'user_cp');

            // TODO: Add default groups and items based on config, group permissions, etc...
        }

        return $this->builtMenu;
    }
}
