<?php

namespace App\Services;

class Menu
{
    protected $menu = [];

    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    public function getMenu()
    {
        return $this->menu;
    }
}
