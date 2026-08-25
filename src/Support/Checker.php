<?php declare(strict_types=1);

namespace EtoA\Support;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\ByteString;

/**
 * One-time token store against multi-submit abuse.
 *
 * Players used to open a form in several browser windows and submit all of them
 * to get the effect more than once for a single cost. A CSRF token does not help
 * there: it stays valid for the whole session, so every window passes.
 *
 * Only the token issued last is accepted, and it is consumed on the first
 * submit. So of any number of windows holding the same page, exactly one submit
 * gets through - the one from the window rendered most recently. Because
 * Symfony locks the session per request, parallel submits are serialised and
 * cannot both consume the same token.
 *
 * Use it through {@see \EtoA\Form\Type\Core\SingleSubmitType}, which issues the
 * token when the form is rendered and verifies it when the form is submitted.
 */
class Checker
{
    private const SESSION_KEY = 'form_checker_token';

    /** Keeps the token stable while one request renders several protected forms. */
    private const REQUEST_KEY = '_form_checker_issued';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Issues the token for the page currently being rendered and invalidates any
     * token issued before. Several protected forms in the same response share one
     * token, so the page as a whole can be submitted once.
     */
    public function issue(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null && $request->attributes->has(self::REQUEST_KEY)) {
            return (string) $request->attributes->get(self::REQUEST_KEY);
        }

        $token = ByteString::fromRandom(32)->toString();
        $this->requestStack->getSession()->set(self::SESSION_KEY, $token);
        $request?->attributes->set(self::REQUEST_KEY, $token);

        return $token;
    }

    /**
     * Verifies the submitted token against the issued one and consumes it, so a
     * second submit of the same payload fails.
     */
    public function consume(?string $submitted): bool
    {
        $session = $this->requestStack->getSession();
        $expected = $session->get(self::SESSION_KEY);

        if (!is_string($expected) || $submitted === null || !hash_equals($expected, $submitted)) {
            // deliberately keep the token: a stale window submitting first must not
            // lock out the window that holds the current token
            return false;
        }

        $session->remove(self::SESSION_KEY);

        return true;
    }
}
