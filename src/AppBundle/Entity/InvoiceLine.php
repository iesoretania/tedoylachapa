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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceLineRepository")
 * @ORM\Table(name="invoice_line")
 * @UniqueEntity({"invoice", "order"})
 */
class InvoiceLine
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $order;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="lines")
     * @ORM\JoinColumn(nullable=false)
     * @var Invoice
     */
    private $invoice;

    /**
     * @ORM\ManyToOne(targetEntity="Reference")
     * @var Reference|null
     */
    private $reference;

    /**
     * @ORM\ManyToOne(targetEntity="Model")
     * @var Model|null
     */
    private $model;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=0)
     * @var int
     */
    private $rate;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=0, max=100)
     * @var int
     */
    private $discount;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return InvoiceLine
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return InvoiceLine
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param Invoice $invoice
     * @return InvoiceLine
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }

    /**
     * @return Reference|null
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param Reference|null $reference
     * @return InvoiceLine
     */
    public function setReference(Reference $reference = null)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return Model|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model|null $model
     * @return InvoiceLine
     */
    public function setModel(Model $model = null)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return InvoiceLine
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param int $rate
     * @return InvoiceLine
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param int $discount
     * @return InvoiceLine
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }
}
