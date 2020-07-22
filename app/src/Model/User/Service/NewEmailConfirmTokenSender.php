<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email as UserEmail;
use RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NewEmailConfirmTokenSender
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment;
     */
    private $twig;

    /**
     * SignUpConfirmTokenSender constructor.
     * @param MailerInterface $mailer
     * @param Environment $twig
     */
    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param UserEmail $email
     * @param string $token
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function send(UserEmail $email, string $token): void
    {
        $subject = 'Подтверждение смены email';
        $email = (new TemplatedEmail())
            ->subject($subject)
            ->to($email->getValue())
            ->htmlTemplate('mail/user/email.html.twig')
            ->context(['token' => $token]);

        try {
            $this->mailer->send($email);
        } catch (TransportException $e) {
            throw new RuntimeException('Невозможно отправить email.');
        }
    }


}
