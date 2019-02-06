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

use AppBundle\Entity\Reference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ReferenceRepository extends ServiceEntityRepository
{
    /**
     * @var ReferenceReceptionRepository
     */
    private $referenceReceptionRepository;

    public function __construct(ManagerRegistry $registry, ReferenceReceptionRepository $referenceReceptionRepository)
    {
        parent::__construct($registry, Reference::class);
        $this->referenceReceptionRepository = $referenceReceptionRepository;
    }

    /**
     * @param $items
     * @return Reference[]
     */
    public function findAllInListById($items) {
        return $this->createQueryBuilder('r')
            ->where('r.id IN (:items)')
            ->setParameter('items', $items)
            ->orderBy('r.code')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Reference[] $list
     * @return mixed
     */
    public function deleteFromList($list)
    {
        // se hace así en lugar de en bloque para que pueda auditarse el borrado
        foreach ($list as $item) {
            // borrar el historial de entrada de material
            $referenceReceptions = $this->referenceReceptionRepository->findBy(['reference' => $item]);
            foreach ($referenceReceptions as $referenceReception) {
                $this->getEntityManager()->remove($referenceReception);
            }
            $this->getEntityManager()->remove($item);
        }
    }
}
