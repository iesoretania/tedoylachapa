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

use AppBundle\Entity\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ModelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'form.code',
                'required' => true
            ])
            ->add('description', TextType::class, [
                'label' => 'form.description',
                'required' => true
            ])
            ->add('file_image', FileType::class, [
                'label' => 'form.image',
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'mimeTypes' => 'image/png',
                        'allowPortrait' => false,
                        'allowLandscape' => false,
                        'allowSquare' => true,
                        'maxSize' => '2M',
                        'minHeight' => 100,
                        'minWidth' => 100
                    ])
                ],
                'required' => false
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'form.active',
                'required' => true,
                'expanded' => true,
                'choices' => [
                    'form.active.yes' => true,
                    'form.active.no' => false
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Model::class,
            'translation_domain' => 'model'
        ]);
    }
}
