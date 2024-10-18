<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ChooseSectorSetupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('checker', HiddenType::class, [
                'data' => $options['data'][0],
            ]);
    }
}