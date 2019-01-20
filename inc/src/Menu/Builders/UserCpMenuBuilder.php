<?php

namespace MyBB\Menu\Builders;

use MyBB\Menu\GroupedMenu;
use MyBB\Menu\Menu;
use MyBB\Menu\MenuBuilderInterface;
use MyBB\Menu\MenuInterface;

class UserCpMenuBuilder implements MenuBuilderInterface
{
    const PRIVATE_MESSAGES_GROUP = 'private_messages';

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
            $this->builtMenu = $this->buildDefaultMenu();
        }

        return $this->builtMenu;
    }

    protected function buildDefaultMenu(): MenuInterface
    {
        $this->lang->load("usercpnav");

        $menu = new Menu($this->plugins, 'user_cp', 'usercp/menu.twig');

        $this->addPrivateMessagesToMenu($menu);

        return $menu;
    }

    protected function addPrivateMessagesToMenu(Menu $menu): void
    {
        if ($this->mybb->settings['enablepms'] == 0 || $this->mybb->usergroup['canusepms'] == 0) {
            return;
        }

        $childMenu = new Menu($this->plugins, 'usercp_private_messages', 'usercp/menu/private_messages.twig');

        if ($this->mybb->usergroup['cansendpms'] == 1) {
            $childMenu->addItem('compose', 0, [
                'link' => 'private.php?action=send',
                'language' => 'ucp_nav_compose',
            ]);
        }

        $folderDetails = explode('$%%$', $this->mybb->user['pmfolders']);
        $folders = [];
        foreach ($folderDetails as $folder) {
            list($folderId, $folderName) = explode('**', $folder, 2);
            $folderName = get_pm_folder_name($folderId, $folderName);

            if ($folderId == 4) {
                $class = "usercp_nav_trash_pmfolder";
            } elseif (!empty($folders)) {
                $class = "usercp_nav_sub_pmfolder";
            } else {
                $class = "usercp_nav_pmfolder";
            }

            $folders[] = [
                'id' => $folderId,
                'name' => $folderName,
                'class' => $class,
            ];
        }

        if (!empty($folders)) {
            $childMenu->addItem('folders', 1, [
                'folders' => $folders,
                'template' => 'usercp/menu/private_messages/folders.twig',
            ]);
        }

        if ($this->mybb->usergroup['cantrackpms']) {
            $childMenu->addItem('tracking', 2, [
                'link' => 'private.php?action=tracking',
                'language' => 'ucp_nav_tracking',
            ]);
        }

        $childMenu->addItem('edit_folders', 3, [
            'link' => 'private.php?action=folders',
            'language' => 'ucp_nav_edit_folders',
        ]);

        $menu->addItem('private_messages', 0, [
            'children' => $childMenu,
            'language' => 'ucp_nav_messenger',
        ]);
    }
}
