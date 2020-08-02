<?php

declare(strict_types=1);

namespace App\Security\Voter\Post;

use App\Model\Post\Entity\Post\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfilePostAccess extends Voter
{
    public const MANAGE = 'edit';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::MANAGE]) && $subject instanceof Post;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return $subject->getAuthorId()->getValue() === $user->getId();
    }
}
