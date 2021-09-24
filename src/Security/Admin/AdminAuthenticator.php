<?php declare(strict_types=1);

namespace EtoA\Security\Admin;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AdminAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private AdminUserProvider $userProvider;
    private AdminAuthenticationSuccessHandler $authenticationSuccessHandler;
    private AdminAuthenticationFailureHandler $authenticationFailureHandler;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(AdminUserProvider $userProvider, AdminAuthenticationSuccessHandler $authenticationSuccessHandler, AdminAuthenticationFailureHandler $authenticationFailureHandler, UrlGeneratorInterface $urlGenerator)
    {
        $this->userProvider = $userProvider;
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
        $this->authenticationFailureHandler = $authenticationFailureHandler;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST')
            && $request->attributes->get('_route') === 'admin.login.check';
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        return new Passport(
            new UserBadge($credentials['username'], function ($username): ?CurrentAdmin {
                return $this->userProvider->loadUserByIdentifier($username);
            }),
            new PasswordCredentials($credentials['password']),
            [
                new CsrfTokenBadge('admin_authenticate', $credentials['csrf_token']),
                new PasswordUpgradeBadge($credentials['password'], $this->userProvider),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->authenticationSuccessHandler->onAuthenticationSuccess($request, $token);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return $this->authenticationFailureHandler->onAuthenticationFailure($request, $exception);
    }

    /**
     * @return array{csrf_token: string, username: string, password: string}
     */
    private function getCredentials(Request $request): array
    {
        $credentials = [
            'csrf_token' => $request->request->get('_csrf_token'),
            'username' => trim($request->request->get('login_nick')),
            'password' => $request->request->get('login_pw'),
        ];

        if (\strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('admin.login'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
