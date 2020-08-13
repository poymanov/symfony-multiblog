<?php

declare(strict_types=1);

namespace App\Widget\Post;

use App\Model\Comment\Entity\Comment\AuthorId;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Post\Entity\Post\Post;
use App\ReadModel\Like\LikeFetcher;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CommentControlsWidget extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('comment_controls', [$this, 'controls'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param Environment        $twig
     * @param array              $comment
     * @param UserInterface|null $user
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function controls(Environment $twig, array $commentData, ?UserInterface $user): string
    {
        $showEditButton = false;
        $showDeleteButton = false;

        if ($user && $commentData['author_id'] == $user->getId()) {
            $commentEntity = $this->createCommentEntity($commentData);
            $showEditButton = $commentEntity->canEdit();
            $showDeleteButton = $commentEntity->canDelete();
        }

        return $twig->render('widget/post/comment_controls.html.twig', [
            'id' => $commentData['id'],
            'showEditButton' => $showEditButton,
            'showDeleteButton' => $showDeleteButton
        ]);
    }

    /**
     * @param array $commentData
     *
     * @return Comment
     * @throws Exception
     */
    private function createCommentEntity(array $commentData): Comment
    {
        return new Comment(
            new Id($commentData['id']),
            new AuthorId($commentData['author_id']),
            new Entity($commentData['entity_type'], $commentData['entity_id']),
            $commentData['text'],
            new DateTimeImmutable($commentData['created_at'])
        );
    }
}
