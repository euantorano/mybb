<?php

namespace MyBB\Twig\Extensions;

use http\Exception\InvalidArgumentException;
use MyBB\Menu\MenuInterface;
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

    public function renderMenu(\Twig_Environment $twig, $nameOrMenu): ?string
    {
        if (is_string($nameOrMenu)) {
            $builder = $this->menuManager->getMenuBuilder($nameOrMenu);

            if (is_null($builder)) {
                return null;
            }

            $menu = $builder->buildMenu();

            return $menu->render($twig);
        } elseif (is_object($nameOrMenu) && ($nameOrMenu instanceof MenuInterface)) {
            return $nameOrMenu->render($twig);
        } else {
            if (is_object($nameOrMenu)) {
                $type = get_class($nameOrMenu);
            } else {
                $type = gettype($nameOrMenu);
            }

            throw new \InvalidArgumentException(
                "Name or menu should either be a menu name or a menu instance, got: {$type}"
            );
        }
    }
}
