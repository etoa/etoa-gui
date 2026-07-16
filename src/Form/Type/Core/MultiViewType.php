<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\UserMulti;
use EtoA\User\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;


class MultiViewType extends AbstractType
{


    public function __construct(
        private readonly UserRepository $userRepository
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('multiUser', TextType::class, [
                'attr' => [
                    'maxlength' => "20",
                    'size' => "20"
                ],
                'constraints' => [new NotNull()],
            ])
            ->add('reason', TextType::class, [
                'attr' => [
                    'maxlength' => "50",
                    'size' => "50"
                ]
            ]);

        $builder->get('multiUser')
            ->addModelTransformer(new CallbackTransformer(
                function ($item): ?\EtoA\Entity\User {
                    return $item;
                },
                function ($nick): ?\EtoA\Entity\User {
                    return $this->userRepository->findOneBy(['nick' => $nick]);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserMulti::class
        ]);
    }
}