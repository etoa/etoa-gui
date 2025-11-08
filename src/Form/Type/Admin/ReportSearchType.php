<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\EntityType;
use EtoA\Form\Type\Core\ReportCategoryType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Form\Type\Core\YesNoMaybeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class, [
                'label' => 'Empfänger',
            ])
            ->add('opponent', UserType::class, [
                'label' => 'Gegespieler',
            ])
            ->add('entity', EntityType::class, [
                'label' => 'Entitiy',
            ])
            ->add('type', ReportCategoryType::class, [
                'label' => 'Kategorie',
            ])
            ->add('read', YesNoMaybeType::class, [
                'label' => 'Gelesen',
            ])
            ->add('deleted', YesNoMaybeType::class, [
                'label' => 'Gelöscht',
            ])
            ->add('archived', YesNoMaybeType::class, [
                'label' => 'Archiviert',
            ]);
    }
}
