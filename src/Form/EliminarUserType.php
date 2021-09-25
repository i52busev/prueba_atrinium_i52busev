<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EliminarUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => 'Email',
                'required' => true
            ))
            ->add('roles', ChoiceType::class, [
                'label' => 'Tipo de usuario',
                'required' => true,
                'multiple' => false,
                'choices' => [
                    'Cliente' => 'ROLE_CLIENTE',
                    'Administrador' => 'ROLE_ADMIN',],
            ])
            ->add('Eliminar', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-default',
                                'style' => 'background-color:DeepPink; border:DeepPink; color:white')
            ))
        ;

        //Conversión para el campo de elección de roles
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                     //Transformar el array en string
                     return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                     //Volver a transformar el string en array
                     return [$rolesString];
                }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
