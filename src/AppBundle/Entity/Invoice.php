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

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 * @ORM\Table(name="invoice")
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @var User|null
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $finalizedOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $finishedOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $servedOn;

    /**
     * @ORM\OneToMany(targetEntity="InvoiceLine", mappedBy="invoice")
     * @ORM\OrderBy({"orderNr":"ASC"})
     * @var InvoiceLine[]
     */
    private $lines;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
        $this->lines = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->dateTime->format('Y') . '/' . $this->getId();
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     * @return Invoice
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return Invoice
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User|null $createdBy
     * @return Invoice
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFinalizedOn()
    {
        return $this->finalizedOn;
    }

    /**
     * @param \DateTime $finalizedOn
     * @return Invoice
     */
    public function setFinalizedOn(\DateTime $finalizedOn = null)
    {
        $this->finalizedOn = $finalizedOn;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFinishedOn()
    {
        return $this->finishedOn;
    }

    /**
     * @param \DateTime $finishedOn
     * @return Invoice
     */
    public function setFinishedOn(\DateTime $finishedOn = null)
    {
        $this->finishedOn = $finishedOn;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getServedOn()
    {
        return $this->servedOn;
    }

    /**
     * @param \DateTime $servedOn
     * @return Invoice
     */
    public function setServedOn(\DateTime $servedOn = null)
    {
        $this->servedOn = $servedOn;
        return $this;
    }

    /**
     * @return InvoiceLine[]
     */
    public function getLines()
    {
        return $this->lines;
    }
}
