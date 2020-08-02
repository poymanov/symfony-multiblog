<?php
declare(strict_types=1);

namespace App\Menu\Profile;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class ProfileMenu
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes(['class' => 'nav nav-pills']);

        $menu->addChild('Профиль', ['route' => 'profile'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link text-info');

        $menu->addChild('Социальные сети', ['route' => 'profile.social'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link text-info');

        $menu->addChild('Публикации', ['route' => 'profile.posts'])
            ->setAttribute('class', 'nav-item')
            ->setLinkAttribute('class', 'nav-link text-info');

        return $menu;
    }
}