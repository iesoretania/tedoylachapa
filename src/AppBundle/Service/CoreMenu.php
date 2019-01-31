<?php
/*
  Copyright (C) 2019: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Service;

use AppBundle\Menu\MenuItem;
use AppBundle\Security\OrganizationVoter;
use Symfony\Component\Security\Core\Security;

class CoreMenu implements MenuBuilderInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return array|null
     */
    public function getMenuStructure()
    {
        $isAdministrator = $this->security->isGranted('ROLE_ADMIN');

        $root = [];

        if ($isAdministrator) {
            $menu1 = new MenuItem();
            $menu1
                ->setName('user')
                ->setRouteName('frontpage')
                ->setCaption('menu.user')
                ->setDescription('menu.user.detail')
                ->setIcon('tools')
                ->setPriority(9000);

            $root[] = $menu1;
        }

        $menu = new MenuItem();
        $menu
            ->setName('personal_data')
            ->setRouteName('personal_data')
            ->setCaption('menu.personal_data')
            ->setDescription('menu.personal_data.detail')
            ->setIcon('clipboard-list')
            ->setPriority(9999);

        $root[] = $menu;

        $menu = new MenuItem();
        $menu
            ->setName('logout')
            ->setRouteName('logout')
            ->setCaption('menu.logout')
            ->setDescription('menu.logout.detail')
            ->setIcon('power-off')
            ->setPriority(10000);

        $root[] = $menu;
        return $root;
    }
}
