<?php

namespace MyBB\Twig\Extensions;

use MyBB\Menu\MenuManager;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var \MyBB\Menu\MenuManager $menuManager
     */
    protected $menuManager;

    public function __construct(MenuManager $menuManager)
    {
        $this->menuManager = $menuManager;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_menu', [$this, 'renderMenu'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ])
        ];
    }

    public function renderMenu(\Twig_Environment $twig, string $name): ?string
    {
        $builder = $this->menuManager->getMenuBuilder($name);

        if (is_null($builder)) {
            return null;
        }

        $menu = $builder->buildMenu();

        return $menu->render($twig);
    }
}
