<?php

namespace App\Form;

use App\Entity\Sector;
use App\Entity\Empresa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EliminarEmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array(
                'label' => 'Nombre',
                'required' => true,
                'disabled' => true
            ))
            ->add('telefono', NumberType::class, array(
                'label' => 'Teléfono',
                'required' => false,
                'disabled' => true
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email',
                'required' => false,
                'disabled' => true
            ))
            ->add('sector', EntityType::class, array(
                'class' => Sector::class,
                'label' => 'Sector',
                'choice_label' => 'nombre',
                'required' => true,
                'disabled' => true
            ))
            ->add('Eliminar', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-default',
                                'style' => 'background-color:DeepPink; border:DeepPink; color:white')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Empresa::class,
        ]);
    }
}
