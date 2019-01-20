<?php

namespace MyBB\Tests\Unit\Menu;

use MyBB\Menu\Menu;
use MyBB\Tests\Unit\TestCase;
use MyBB\Tests\Unit\Traits\LegacyCoreAwareTest;

class MenuTest extends TestCase
{
    use LegacyCoreAwareTest;

    public static function setUpBeforeClass()
    {
        self::setupMybb();
    }

    public function testAddItem()
    {
        $menu = new Menu($GLOBALS['plugins'], 'test', '');

        $menu->addItem('item_1');

        $this->assertTrue($menu->containsItem('item_1'));
        $this->assertEquals(1, $menu->count());
        $this->assertEquals(0, $menu->getHighestPosition());
    }

    public function testRemoveItem()
    {
        $menu = new Menu($GLOBALS['plugins'], 'test', '');

        $menu->addItem('item_1');

        $this->assertEquals(1, $menu->count());

        $menu->removeItem('item_1');

        $this->assertEquals(0, $menu->count());
        $this->assertEquals(-1, $menu->getHighestPosition());
    }

    public function testIteratorOrder()
    {
        $menu = new Menu($GLOBALS['plugins'], 'test', '');

        $menu->addItem('item_1', 2);
        $menu->addItem('item_2', 0);
        $menu->addItem('item_3', 1);

        $lastMax = -1;
        foreach ($menu as $item) {
            $this->assertGreaterThan($lastMax, $item['order']);
        }
    }
}