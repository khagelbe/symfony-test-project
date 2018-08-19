<?php

// src/AppBundle/Form/UpdateJobType.php
namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Job;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateJobType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => false
            ])
            ->add('title', TextType::class, [
                'required' => false
            ])
            ->add('zip', TextType::class, [
                'required' => false
            ])
            ->add('description', TextType::class, [
                'required' => false
            ])
            ->add('dueDate', DateType::class, [
                 'widget' => 'single_text',
                 'format' => 'yyyy-MM-dd',
                'required' => false
             ])
            ->setMethod('PUT');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}