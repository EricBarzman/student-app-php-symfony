<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private RateLimiterFactory $_login_local_mainLimiter){}

    public function authenticate(Request $request): Passport
    {
        $pincode = $request->request->get('pincode', '');
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $pincode);
        return new Passport(
            new UserBadge($pincode),
            new PasswordCredentials($request->request->get('pincode', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationFailure(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception): Response
    {
        $limiter = $this->_login_local_mainLimiter->create($request->getClientIp());
        $limiter->consume();
        return new RedirectResponse('/login');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        //     return new RedirectResponse($targetPath);
        // }
        // For example:
        $limiter = $this->_login_local_mainLimiter->create($request->getClientIp());
        $limiter->reset();
        return new RedirectResponse('/');
        // throw new \Exception('/'.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
