<?php declare(strict_types=1);

namespace EtoA\Tests\Support;

use EtoA\Form\Type\Core\SingleSubmitType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * The one-time token that keeps a form from being submitted more than once,
 * see {@see \EtoA\Support\Checker}.
 */
class CheckerTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;
    private RequestStack $requestStack;
    private SessionInterface $session;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->formFactory = self::getContainer()->get(FormFactoryInterface::class);
        $this->requestStack = self::getContainer()->get(RequestStack::class);
        $this->session = new Session(new MockArraySessionStorage());
    }

    /** Renders the form and returns the token handed out. */
    private function render(): string
    {
        $this->push('GET');
        $value = $this->form()->createView()->children['checker']->vars['value'];
        $this->requestStack->pop();

        return (string) $value;
    }

    /** Submits the form with the given token and returns whether it was accepted. */
    private function submit(?string $token): bool
    {
        $payload = ['name' => 'irgendwas'];
        if ($token !== null) {
            $payload['checker'] = $token;
        }

        $this->push('POST', ['f' => $payload]);
        $form = $this->form();
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $valid = $form->isValid();
        $this->requestStack->pop();

        return $valid;
    }

    /**
     * @param array<string, mixed> $post
     */
    private function push(string $method, array $post = []): void
    {
        $request = Request::create('/game/test', $method, $post);
        $request->setSession($this->session);
        $this->requestStack->push($request);
    }

    private function form(): FormInterface
    {
        // csrf off: this test is about the one-time token, not about CSRF
        return $this->formFactory
            ->createNamedBuilder('f', FormType::class, null, ['csrf_protection' => false])
            ->add('name', TextType::class, ['required' => false])
            ->add('checker', SingleSubmitType::class)
            ->add('go', SubmitType::class)
            ->getForm();
    }

    public function testRenderingIssuesAToken(): void
    {
        $this->assertNotSame('', $this->render());
    }

    public function testTheFreshlyRenderedFormIsAccepted(): void
    {
        $this->assertTrue($this->submit($this->render()));
    }

    public function testTheSameTokenIsRejectedTheSecondTime(): void
    {
        $token = $this->render();

        $this->assertTrue($this->submit($token), 'first submit');
        $this->assertFalse($this->submit($token), 'second submit of the same payload');
    }

    public function testASubmitWithoutATokenIsRejected(): void
    {
        $this->render();

        $this->assertFalse($this->submit(null));
    }

    /**
     * The abuse this exists for: the same page open in several windows, each of
     * them submitted. Only the window rendered last may get through.
     */
    public function testOnlyTheWindowRenderedLastIsAccepted(): void
    {
        $first = $this->render();
        $second = $this->render();
        $this->assertNotSame($first, $second, 'each render issues its own token');

        $this->assertFalse($this->submit($first), 'stale window');
        $this->assertTrue($this->submit($second), 'current window');
    }

    /** A stale window submitting first must not lock out the current one. */
    public function testAStaleSubmitDoesNotInvalidateTheCurrentToken(): void
    {
        $stale = $this->render();
        $current = $this->render();

        $this->assertFalse($this->submit($stale));
        $this->assertFalse($this->submit('völliger unsinn'));
        $this->assertTrue($this->submit($current), 'the current token still works');
    }

    /** Otherwise a player could not correct a rejected input. */
    public function testARejectedSubmitIsRenderedWithAFreshToken(): void
    {
        $this->render();

        $this->push('POST', ['f' => ['name' => 'x', 'checker' => 'falsch']]);
        $form = $this->form();
        $form->handleRequest($this->requestStack->getCurrentRequest());
        $this->assertFalse($form->isValid());
        $reissued = (string) $form->createView()->children['checker']->vars['value'];
        $this->requestStack->pop();

        $this->assertNotSame('', $reissued);
        $this->assertNotSame('falsch', $reissued);
        $this->assertTrue($this->submit($reissued));
    }

    /** Two protected forms in one response share the token of that response. */
    public function testFormsRenderedTogetherShareTheToken(): void
    {
        $this->push('GET');
        $first = $this->form()->createView()->children['checker']->vars['value'];
        $second = $this->form()->createView()->children['checker']->vars['value'];
        $this->requestStack->pop();

        $this->assertSame($first, $second);
        $this->assertTrue($this->submit((string) $first));
    }
}
