<?php

namespace App\Form;

use App\Entity\Celda;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CeldaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fila')
            ->add('columna')
            ->add('cantidadMesas')
            ->add('cantidadButacas')
            ->add('categoriaButaca')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Celda::class,
        ]);
    }
}
