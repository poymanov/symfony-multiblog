<?php

declare(strict_types=1);

namespace App\Tests\Functional\Fixtures;

use App\DataFixtures\UserFixture as CommonUserFixture;
use App\Model\Like\Entity\Like\AuthorId;
use App\Model\Like\Entity\Like\Entity;
use App\Model\Like\Entity\Like\Id;
use App\Model\Like\Entity\Like\Like;
use App\Model\Post\Entity\Post\Post;
use App\Model\User\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LikeFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var Post $post */
        /** @var User $user */
        $post = $this->getReference(PostFixture::REFERENCE_POST);
        $user = $this->getReference(CommonUserFixture::REFERENCE_USER);

        $like = new Like(
            Id::next(),
            new AuthorId($user->getId()->getValue()),
            new Entity(Post::class, $post->getId()->getValue())
        );

        $manager->persist($like);
        $manager->flush();

    }

    /**
     * @return array|string[]
     */
    public function getDependencies(): array
    {
        return [
            PostFixture::class,
            CommonUserFixture::class,
        ];
    }
}
