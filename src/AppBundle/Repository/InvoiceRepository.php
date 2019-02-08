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

use AppBundle\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    /**
     * @param $items
     * @return Invoice[]
     */
    public function findNotFinalizedInListById($items) {
        return $this->createQueryBuilder('i')
            ->where('i.id IN (:items)')
            ->andWhere('i.finalizedOn IS NULL')
            ->setParameter('items', $items)
            ->orderBy('i.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Invoice[] $list
     */
    public function deleteFromList($list)
    {
        // se hace así en lugar de en bloque para que pueda auditarse el borrado
        foreach ($list as $item) {
            foreach ($item->getLines() as $line) {
                $this->getEntityManager()->remove($line);
            }
            $this->getEntityManager()->remove($item);
        }
    }
}
