<?php

namespace App\Form;

use App\Entity\Sector;
use App\Entity\ClienteSector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ClienteSectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('id_cliente')
            ->add('id_sector', EntityType::class, array(
                'class' => Sector::class,
                'label' => 'Sector',
                'choice_label' => 'nombre',
                'required' => true,
                'help' => 'Este campo es obligatorio'
            ))
            ->add('Vincular', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-default',
                                'style' => 'background-color:DeepPink; border:DeepPink; color:white')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ClienteSector::class,
        ]);
    }
}
