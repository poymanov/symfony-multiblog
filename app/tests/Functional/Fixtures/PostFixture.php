<?php

declare(strict_types=1);

namespace App\Tests\Functional\Fixtures;

use App\DataFixtures\UserFixture;
use App\Model\Post\Entity\Post\AuthorId;
use App\Model\Post\Entity\Post\Id;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\Post\PostBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class PostFixture extends Fixture
{
    public const POST_1_ID = '00000000-0000-0000-0000-000000000001';

    public const POST_2_ID = '00000000-0000-0000-0000-000000000002';

    public const POST_3_ID = '00000000-0000-0000-0000-000000000003';

    public const POST_4_ID = '00000000-0000-0000-0000-000000000004';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference(UserFixture::REFERENCE_USER);

        $draftPost = (new PostBuilder())
            ->withId(new Id(self::POST_1_ID))
            ->withAuthorId(new AuthorId($user->getId()->getValue()))
            ->draft()
            ->withTitle('Test')
            ->withPreviewText('Preview Text')
            ->withText('Text')
            ->build();

        $manager->persist($draftPost);
        $manager->flush();

        $anotherDraftPost = (new PostBuilder())
            ->withId(new Id(self::POST_2_ID))
            ->draft()
            ->withTitle('Another Test')
            ->build();

        $manager->persist($anotherDraftPost);
        $manager->flush();

        $publishedPost = (new PostBuilder())
            ->withId(new Id(self::POST_3_ID))
            ->withAuthorId(new AuthorId($user->getId()->getValue()))
            ->published()
            ->withTitle('Published Test')
            ->build();

        $manager->persist($publishedPost);
        $manager->flush();

        $anotherPublishedPost = (new PostBuilder())
            ->withId(new Id(self::POST_4_ID))
            ->published()
            ->withTitle('Another Published Test')
            ->build();

        $manager->persist($anotherPublishedPost);
        $manager->flush();
    }

    /**
     * @return array|string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
