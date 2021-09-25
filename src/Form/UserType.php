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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array(
                'label' => 'Email',
                'required' => true,
                'help' => 'Este campo es obligatorio'
            ))
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Ambas contraseñas deben coincidir.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Confirmar Contraseña'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Tipo de usuario',
                'required' => true,
                'help' => 'Este campo es obligatorio',
                'multiple' => false,
                'choices' => [
                    'Cliente' => 'ROLE_CLIENTE',
                    'Administrador' => 'ROLE_ADMIN',],
            ])
            ->add('Registrar', SubmitType::class, array(
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
