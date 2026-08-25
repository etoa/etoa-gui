<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Support\Checker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Hidden one-time token that makes a form submittable exactly once, see
 * {@see Checker} for the abuse it prevents.
 *
 * Add it to any form whose submit has an effect that must not happen twice:
 *
 *     $builder->add('checker', SingleSubmitType::class);
 *
 * The token is issued in buildView(), so only rendering a form hands one out - a
 * request that submits and redirects never issues one, and a submit that fails
 * validation gets a fresh one with the re-rendered form. Verification happens on
 * PRE_SUBMIT, before buildView() of the same request could replace the token.
 */
class SingleSubmitType extends AbstractType
{
    public function __construct(
        private readonly Checker $checker,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options): void {
            $submitted = $event->getData();
            if ($this->checker->consume(is_string($submitted) ? $submitted : null)) {
                return;
            }

            // the error belongs on the form as a whole, the field itself is hidden
            $form = $event->getForm();
            ($form->getParent() ?? $form)->addError(new FormError($options['invalid_message']));
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // buildView() only runs when the form is actually rendered, so a request
        // that submits and redirects never issues a token. A submit that comes
        // back with errors is re-rendered and does get a fresh one, otherwise the
        // player could not correct the input.
        $view->vars['value'] = $this->checker->issue();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
            'required' => false,
            'label' => false,
            'invalid_message' => 'Diese Seite kann nicht mehrfach abgeschickt werden. Bitte lade sie neu.',
        ]);
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }
}
