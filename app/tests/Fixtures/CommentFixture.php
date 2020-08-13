<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\DataFixtures\UserFixture as CommonUserFixture;
use App\Model\Comment\Entity\Comment\AuthorId;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\Entity\Comment\Entity;
use App\Model\Comment\Entity\Comment\Id;
use App\Model\Post\Entity\Post\Post;
use App\Model\Post\Entity\Post\Id as PostId;
use App\Model\User\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixture extends Fixture implements DependentFixtureInterface
{
    public const COMMENT_1_ID = '00000000-0000-0000-0000-000000000001';

    public const COMMENT_2_ID = '00000000-0000-0000-0000-000000000002';

    public const COMMENT_3_ID = '00000000-0000-0000-0000-000000000003';

    public const COMMENT_4_ID = '00000000-0000-0000-0000-000000000004';

    public const COMMENT_5_ID = '00000000-0000-0000-0000-000000000005';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        /** @var Post $post */
        /** @var User $user */
        /** @var User $testUser */
        $post        = $this->getReference(PostFixture::REFERENCE_POST_2);
        $user        = $this->getReference(CommonUserFixture::REFERENCE_USER);
        $testUser    = $this->getReference(UserFixture::REFERENCE_USER);

        $entity   = new Entity(Post::class, $post->getId()->getValue());
        $draftedEntity = new Entity(Post::class, PostFixture::POST_1_ID);
        $notExistedEntity = new Entity(Post::class, PostId::next()->getValue());

        $authorId = new AuthorId($user->getId()->getValue());

        $date = new DateTimeImmutable();

        $firstComment = new Comment(new Id(self::COMMENT_1_ID), $authorId, $entity, 'First Comment', $date);
        $manager->persist($firstComment);

        $secondComment = new Comment(new Id(self::COMMENT_2_ID), new AuthorId($testUser->getId()->getValue()), $entity, 'Second Comment',
            $date->modify('+1 minutes'));
        $manager->persist($secondComment);

        $editedComment = new Comment(Id::next(), $authorId, $entity, 'Third Comment', $date->modify('+2 minutes'));
        $editedComment->edit(new DateTimeImmutable('10-08-2099 21:55'), 'Edited Comment');
        $manager->persist($editedComment);

        $oldComment = new Comment(new Id(self::COMMENT_3_ID), $authorId, $entity, 'Old Comment',
            new DateTimeImmutable('-2 days'));
        $manager->persist($oldComment);

        $commentForDraftedPost = new Comment(new Id(self::COMMENT_4_ID), $authorId, $draftedEntity, 'Comment for drafted post', $date);
        $manager->persist($commentForDraftedPost);

        $commentForNotExistedPost = new Comment(new Id(self::COMMENT_5_ID), $authorId, $notExistedEntity, 'Comment for drafted post', $date);
        $manager->persist($commentForNotExistedPost);

        for ($i = 0; $i < 20; $i++) {
            $comment = new Comment(Id::next(), $authorId,
                $entity, $text = $faker->sentence,
                $date->modify('+' . ($i + 3) . 'minutes')
            );

            $manager->persist($comment);
        }

        $lastComment = new Comment(Id::next(), $authorId, $entity, 'Last Comment', new DateTimeImmutable('10-08-2100 22:55'));
        $manager->persist($lastComment);

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
