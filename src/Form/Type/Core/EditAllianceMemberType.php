<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAllianceMemberType extends AbstractType
{
    public function __construct(
        private readonly Security       $security,
        private readonly AllianceRankRepository $allianceRankRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'allianceRank',ChoiceType::class, [
                'choices'  => $this->allianceRankRepository->findBy(['alliance'=>$this->security->getUser()->getData()->getAlliance()]),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => 'Rang wählen...'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}