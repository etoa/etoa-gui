<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class EditShipListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('count', IntegerType::class, [
                'label' => false,
                'attr' => ['size' => 4],
            ])
            ->add('bunkered', IntegerType::class, [
                'label' => false,
                'attr' => ['size' => 4],
            ])
        ;
    }
}
