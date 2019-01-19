<?php

namespace MyBB\Tests\Unit\Menu;

use MyBB\Menu\MenuBuilderInterface;
use MyBB\Menu\MenuInterface;
use MyBB\Menu\MenuManager;
use MyBB\Tests\Unit\TestCase;
use Psr\Container\ContainerInterface;

class MenuManagerTest extends TestCase
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var MenuBuilderInterface
     */
    private $mockBuilder;

    public function setUp()
    {
        $this->container = \Mockery::mock(ContainerInterface::class);

        $this->mockBuilder = function () {
            $menuMock = \Mockery::mock(MenuInterface::class);

            $mock = \Mockery::mock(MenuBuilderInterface::class);

            $mock->shouldReceive('buildMenu')->andReturn($menuMock);

            return $mock;
        };
    }

    public function testAddCallableBuilder()
    {
        $menuManager = new MenuManager($this->container);

        $menuManager->addMenuBuilder('usercp', $this->mockBuilder);

        $this->assertTrue($menuManager->doesMenuBuilderExist('usercp'));
    }

    public function testAddInstanceBuilder()
    {
        $menuManager = new MenuManager($this->container);

        $menuManager->addMenuBuilder('class', $this->mockBuilder);

        $this->assertTrue($menuManager->doesMenuBuilderExist('class'));
    }

    public function testAddInvalidBuilder()
    {
        $menuManager = new MenuManager($this->container);

        $this->expectException(\InvalidArgumentException::class);

        $menuManager->addMenuBuilder('invalid', []);
    }

    public function testGetMenuBuilderThatExists()
    {
        $menuManager = new MenuManager($this->container);

        $menuManager->addMenuBuilder('usercp', $this->mockBuilder);

        $builder = $menuManager->getMenuBuilder('usercp');

        $this->assertNotNull($builder);
        $this->assertInstanceOf(MenuBuilderInterface::class, $builder);
    }

    public function testGetMenuBuilderThatDoesNotExist()
    {
        $menuManager = new MenuManager($this->container);

        $menuManager->addMenuBuilder('usercp', $this->mockBuilder);

        $this->assertNull($menuManager->getMenuBuilder('invalid'));
    }

    public function testGetMenuBuilders()
    {
        $menuManager = new MenuManager($this->container);

        $menuManager->addMenuBuilder('usercp', $this->mockBuilder);

        $builders = $menuManager->getMenuBuilders();

        $this->assertIsArray($builders);
        $this->assertArrayHasKey('usercp', $builders);
        $this->assertCount(1, $builders);
    }
}
