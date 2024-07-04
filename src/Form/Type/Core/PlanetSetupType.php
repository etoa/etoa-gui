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
        $builder
            ->add('filter_sol_id', SolarTypeType::class, [
                'placeholder' => 'Bitte wählen...',
                'label' => false,
                'required' => false,
                'data' => $request->request->all('planet_setup')?$request->request->all('planet_setup')['filter_sol_id']:null
            ])
            ->add('filter_planet_id', PlanetTypeType::class, [
                'placeholder' => 'Bitte wählen...',
                'label' => false,
                'required' => false,
                'data' => $request->request->all('planet_setup')?$request->request->all('planet_setup')['filter_planet_id']:null
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
            ->add('planet_id', HiddenType::class, [
                'data' => $options['data']['planet_id']
            ])
            ->add('checker', HiddenType::class, [
                'data' => $options['data']['checker']
            ]);
    }
}