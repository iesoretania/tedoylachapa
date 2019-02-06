<?php
/*
  Copyright (C) 2018-2019: Luis Ramón López López

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

namespace AppBundle\Repository;

use AppBundle\Entity\ReferenceReception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ReferenceReceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReferenceReception::class);
    }

    /**
     * @param $items
     * @return ReferenceReception[]
     */
    public function findAllInListById($items) {
        return $this->createQueryBuilder('r')
            ->where('r.id IN (:items)')
            ->setParameter('items', $items)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ReferenceReception[] $list
     * @return mixed
     */
    public function deleteFromList($list)
    {
        // se hace así en lugar de en bloque para que pueda auditarse el borrado
        foreach ($list as $item) {
            $this->getEntityManager()->remove($item);
        }
    }
}
