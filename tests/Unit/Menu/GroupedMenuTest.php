<?php

namespace MyBB\Tests\Unit\Menu;

use MyBB\Menu\GroupedMenu;
use MyBB\Tests\Unit\TestCase;
use MyBB\Tests\Unit\Traits\LegacyCoreAwareTest;

class GroupedMenuTest extends TestCase
{
    use LegacyCoreAwareTest;

    public static function setUpBeforeClass()
    {
        static::setupMybb();
    }

    public function testAddGroup()
    {
        $menu = new GroupedMenu($GLOBALS['plugins'], 'test');

        $menu->addGroup('main');

        $this->assertTrue($menu->containsGroup('main'));
        $this->assertEquals(1, $menu->count());
    }

    public function testRemoveGroup()
    {
        $menu = new GroupedMenu($GLOBALS['plugins'], 'test');

        $menu->addGroup('main');

        $this->assertTrue($menu->containsGroup('main'));
        $this->assertEquals(1, $menu->count());

        $menu->removeGroup('main');

        $this->assertFalse($menu->containsGroup('main'));
        $this->assertEquals(0, $menu->count());
    }

    public function testAddItemToGroup()
    {
        $menu = new GroupedMenu($GLOBALS['plugins'], 'test');

        $menu->addItemToGroup('main', 'user cp');

        $this->assertTrue($menu->containsGroup('main'));
        $this->assertEquals(1, $menu->count());

        $this->assertTrue($menu->groupContainsItem('main', 'user cp'));
    }

    public function testRemoveItemFromGroup()
    {
        $menu = new GroupedMenu($GLOBALS['plugins'], 'test');

        $menu->addItemToGroup('main', 'user cp');

        $this->assertTrue($menu->containsGroup('main'));
        $this->assertEquals(1, $menu->count());

        $this->assertTrue($menu->groupContainsItem('main', 'user cp'));

        $menu->removeItemFromGroup('main', 'user cp');

        $this->assertTrue($menu->containsGroup('main'));

        $this->assertFalse($menu->groupContainsItem('main', 'user cp'));
    }
}