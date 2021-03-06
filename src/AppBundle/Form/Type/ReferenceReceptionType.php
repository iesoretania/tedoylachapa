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

use AppBundle\Entity\Reference;
use AppBundle\Entity\ReferenceReception;
use AppBundle\Repository\ReferenceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferenceReceptionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'form.date',
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('reference', EntityType::class, [
                'label' => 'form.reference',
                'class' => Reference::class,
                'placeholder' => 'form.select_reference',
                'query_builder' => function (ReferenceRepository $referenceRepository) {
                    return $referenceRepository
                        ->createQueryBuilder('r')
                        ->addOrderBy('r.code')
                        ->addOrderBy('r.description');
                },
                'required' => true
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'form.quantity',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'form.description',
                'required' => true
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReferenceReception::class,
            'translation_domain' => 'reference_reception'
        ]);
    }
}
