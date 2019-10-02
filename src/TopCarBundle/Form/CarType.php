<?php

namespace TopCarBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use TopCarBundle\Entity\Body;
use TopCarBundle\Entity\Brand;
use TopCarBundle\Entity\Fuel;

class CarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('brand', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name'
            ])
            ->add('model', TextType::class)
            ->add('body', EntityType::class, [
                'class' => Body::class,
                'choice_label' => 'type'
            ])
            ->add('year', NumberType::class)
            ->add('fuel', EntityType::class, [
                'class' => Fuel::class,
                'choice_label' => 'type'
            ])
            ->add('power', NumberType::class)
            ->add('topSpeed', NumberType::class)
            ->add('acceleration', NumberType::class)
            ->add('image', FileType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new \Symfony\Component\Validator\Constraints\File([
                            'maxSize' => '1024k',
                            'mimeTypes' => 'image/*',
                            'mimeTypesMessage' => 'Please upload a valid image file'
                        ])
                    ]
                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TopCarBundle\Entity\Car',
            'allow_extra_fields' => true
        ));
    }

}
