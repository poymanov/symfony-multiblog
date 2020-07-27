<?php

declare(strict_types=1);

namespace App\Controller\Profile\Social\OAuth;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\Network\Detach\Command;
use App\Model\User\UseCase\Network\Detach\Handler;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/oauth")
 */
class DetachController extends AbstractController
{
    /**
     * @var ErrorHandler
     */
    private $errors;

    /**
     * @param ErrorHandler $errors
     */
    public function __construct(ErrorHandler $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @Route("/detach/{network}/{identity}", name="profile.oauth.detach", methods={"DELETE"})
     * @param Request $request
     * @param string $network
     * @param string $identity
     * @param Handler $handler
     * @return Response
     */
    public function detach(Request $request, string $network, string $identity, Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('profile.social');
        }

        $command = new Command(
            $this->getUser()->getId(),
            $network,
            $identity
        );

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Facebook отключен.');
            return $this->redirectToRoute('profile.social');
        } catch (DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('profile.social');
        }
    }
}
