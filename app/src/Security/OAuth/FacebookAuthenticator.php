<?php

declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\User\UseCase\Network\Auth\Command;
use App\Model\User\UseCase\Network\Auth\Handler;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FacebookAuthenticator extends SocialAuthenticator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ClientRegistry
     */
    private $clients;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * FacebookAuthenticator constructor.
     * @param UrlGeneratorInterface $urlGenerator
     * @param ClientRegistry $clients
     * @param Handler $handler
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, ClientRegistry $clients, Handler $handler)
    {
        $this->urlGenerator = $urlGenerator;
        $this->clients = $clients;
        $this->handler = $handler;
    }


    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'oauth.facebook_check';
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $facebookUser = $this->getFacebookClient()->fetchUserFromToken($credentials);

        $network = 'facebook';
        $id = $facebookUser->getId();
        $username = $network . ':' . $id;

        try {
            return $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e) {
            $command = new Command($network, $id);
            $command->firstName = $facebookUser->getFirstName();
            $command->lastName = $facebookUser->getLastName();

            $this->handler->handle($command);
            return $userProvider->loadUserByUsername($username);
        }
    }

    /**
     * @return FacebookClient
     */
    private function getFacebookClient(): FacebookClient
    {
        return $this->clients->getClient('facebook_main');
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }
}
