<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\SignUp;
use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SignUpController extends AbstractController
{
    /**
     * @var ErrorHandler
     */
    private $errors;

    /**
     * @var UserFetcher
     */
    private $users;

    /**
     * @param ErrorHandler $errors
     * @param UserFetcher  $users
     */
    public function __construct(ErrorHandler $errors, UserFetcher $users)
    {
        $this->errors = $errors;
        $this->users  = $users;
    }

    /**
     * @Route("/signup", name="auth.signup")
     * @param Request                $request
     *
     * @param SignUp\Request\Handler $handler
     *
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function request(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = new SignUp\Request\Command();

        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Проверьте ваш email.');
                return $this->redirectToRoute('home');
            } catch (DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/{token}", name="auth.signup.confirm")
     * @param Request $request
     * @param string $token
     * @param SignUp\Confirm\ByToken\Handler $handler
     * @return Response
     */
    public function confirm(
        Request $request,
        string $token,
        SignUp\Confirm\ByToken\Handler $handler,
        UserProviderInterface $userProvider,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response
    {
        if (!$user = $this->users->findBySignUpConfirmToken($token)) {
            $this->addFlash('error', 'Неизвестный или уже подтвержденный токен.');
            return $this->redirectToRoute('auth.signup');
        }

        $command = new SignUp\Confirm\ByToken\Command($token);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Ваш email успешно подтвержден.');
            return $guardHandler->authenticateUserAndHandleSuccess(
                $userProvider->loadUserByUsername($user->email),
                $request,
                $authenticator,
                'main'
            );
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('auth.signup');
        }
    }
}
