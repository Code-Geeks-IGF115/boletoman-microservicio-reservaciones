<?php

namespace App\Form;

use App\Entity\CategoriaButaca;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriaButacaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo')
            ->add('precioUnitario')
            ->add('nombre')
            ->add('salaDeEventos')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoriaButaca::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
