<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Support\StringUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditEmptySpaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastVisited', TextType::class, [
                'label' => 'Zuletzt besucht',
                'disabled' => 'disabled',
            ])
            ->get('lastVisited')
            ->addModelTransformer(new CallbackTransformer(
                function (int $timestamp): string {
                    return $timestamp === 0 ? 'Nie' : StringUtils::formatDate($timestamp);
                },
                fn (string $value) => $value
            ))
        ;
    }
}
