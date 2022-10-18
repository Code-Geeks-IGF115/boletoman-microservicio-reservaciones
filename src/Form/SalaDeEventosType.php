<?php

namespace App\Form;

use App\Entity\SalaDeEventos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalaDeEventosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('direccion')
            ->add('telefono')
            ->add('email')
            ->add('forma')
            ->add('filas')
            ->add('columnas');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalaDeEventos::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
