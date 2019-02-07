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

namespace AppBundle\Form\Type;

use AppBundle\Entity\InvoiceLine;
use AppBundle\Entity\Model;
use AppBundle\Entity\Reference;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceLineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', EntityType::class, [
                'label' => 'form.line.reference',
                'class' => Reference::class,
                'placeholder' => 'form.line.no_reference',
                'required' => false
            ])
            ->add('model', EntityType::class, [
                'label' => 'form.line.model',
                'class' => Model::class,
                'placeholder' => 'form.line.no_model',
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => 'form.line.description',
                'required' => true
            ])
            ->add('rate', MoneyType::class, [
                'label' => 'form.line.rate',
                'divisor' => 100,
                'required' => true
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'form.line.quantity',
                'required' => true
            ])
            ->add('discount', IntegerType::class, [
                'label' => 'form.line.discount',
                'required' => true
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InvoiceLine::class,
            'translation_domain' => 'invoice'
        ]);
    }
}
