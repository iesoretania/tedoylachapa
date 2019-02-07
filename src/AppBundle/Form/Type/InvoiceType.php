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

use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateTime', DateTimeType::class, [
                'label' => 'form.date_time',
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('createdBy', EntityType::class, [
                'label' => 'form.created_by',
                'class' => User::class,
                'disabled' => true,
                'required' => true
            ])
            ->add('finalizedOn', DateTimeType::class, [
                'label' => 'form.finalized_on',
                'disabled' => true,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('finishedOn', DateTimeType::class, [
                'label' => 'form.finished_on',
                'disabled' => true,
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('servedOn', DateTimeType::class, [
                'label' => 'form.served_on',
                'disabled' => true,
                'widget' => 'single_text',
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'translation_domain' => 'invoice'
        ]);
    }
}
