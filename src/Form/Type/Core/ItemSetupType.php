<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ItemSetupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('itemset_id', DefaultItemType::class, [
                'placeholder' => 'Bitte wählen...',
                'label' => false,
                'required' => true,
            ])
            ->add('choose', SubmitType::class, [
                'label' => 'Weiter',
            ]);
    }
}