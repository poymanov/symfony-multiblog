<?php

declare(strict_types=1);

namespace App\Widget\Post;

use App\Model\Post\Entity\Post\Post;
use App\ReadModel\Like\LikeFetcher;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LikePanelWidget extends AbstractExtension
{
    private LikeFetcher $likes;

    /**
     * @param LikeFetcher $likes
     */
    public function __construct(LikeFetcher $likes)
    {
        $this->likes = $likes;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('post_like_panel', [$this, 'likePanel'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param Environment        $twig
     * @param Post               $post
     * @param UserInterface|null $user
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function likePanel(Environment $twig, Post $post, ?UserInterface $user): string
    {
        $likeButtonMode = $this->getLikeButtonMode($post, $user);
        $likes = $this->getLikesCount($post);

        return $twig->render('widget/post/like_panel.html.twig', [
            'mode'  => $likeButtonMode,
            'alias' => $post->getAlias(),
            'likes' => $likes,
        ]);
    }

    /**
     * Получение режима кнопки управления лайком
     *
     * @param Post               $post
     * @param UserInterface|null $user
     *
     * @return string
     */
    private function getLikeButtonMode(Post $post, ?UserInterface $user): string
    {
        if (is_null($user)) {
            return '';
        } else if ($this->likes->existsByEntityAndAuthorId(
            Post::class,
            $post->getId()->getValue(),
            $user->getId()
        )) {
            return 'delete';
        } elseif ($post->getAuthorId()->getValue() != $user->getId()) {
            return 'create';
        }

        return '';
    }

    /**
     * Количество лайков по публикации
     *
     * @param Post $post
     *
     * @return int
     */
    private function getLikesCount(Post $post): int
    {
        return $this->likes->countByEntity(Post::class, $post->getId()->getValue());
    }
}
