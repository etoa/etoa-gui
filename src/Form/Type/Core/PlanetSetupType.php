<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RequestStack;

class PlanetSetupType extends AbstractType
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options, ): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $builder
            ->add('filter_sol_id', SolarTypeType::class, [
                'placeholder' => 'Bitte wählen...',
                'label' => false,
                'required' => false,
                'data' => $session->get('filter_sol_id')
            ])
            ->add('filter_planet_id', PlanetTypeType::class, [
                'placeholder' => 'Bitte wählen...',
                'label' => false,
                'required' => false,
                'data' => $session->get('filter_planet_id')
            ])
            ->add('submit_chooseplanet', SubmitType::class, [
                'label' => 'Weiter',
            ])
            ->add('new_planet', SubmitType::class, [
                'label' => 'Einen neuen Planeten auswählen',
            ])
            ->add('redo', SubmitType::class, [
                'label' => 'Einen neuen Sektor auswählen',
            ])
            ->add('checker', HiddenType::class, [
                'data' => $options['data']['checker']
            ]);
    }
}