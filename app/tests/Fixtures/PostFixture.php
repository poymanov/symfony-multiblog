<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use App\DataFixtures\UserFixture as CommonUserFixture;
use App\Model\Post\Entity\Post\AuthorId;
use App\Model\Post\Entity\Post\Id;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\Post\PostBuilder;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class PostFixture extends Fixture implements DependentFixtureInterface
{
    public const POST_1_ID = '00000000-0000-0000-0000-000000000001';

    public const POST_2_ID = '00000000-0000-0000-0000-000000000002';

    public const POST_3_ID = '00000000-0000-0000-0000-000000000003';

    public const POST_4_ID = '00000000-0000-0000-0000-000000000004';

    public const REFERENCE_POST = 'post_with_like';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        /** @var User $anotherUser */
        $user = $this->getReference(CommonUserFixture::REFERENCE_USER);
        $anotherUser = $this->getReference(UserFixture::REFERENCE_USER_2);

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
            ->withAuthorId(new AuthorId($anotherUser->getId()->getValue()))
            ->published()
            ->withTitle('Another Published Test')
            ->build();

        $manager->persist($anotherPublishedPost);
        $manager->flush();

        /** @var User $testUser */
        $testUser = $this->getReference(UserFixture::REFERENCE_USER);

        $testUserPublishedPost = (new PostBuilder())
            ->published((new DateTimeImmutable())->sub(new DateInterval('P1D')))
            ->withAuthorId(new AuthorId($testUser->getId()->getValue()))
            ->withTitle('Published Test Title')
            ->withPreviewText('Published Test Preview Text')
            ->withText('Published Test Text')
            ->build();

        $this->setReference(self::REFERENCE_POST, $testUserPublishedPost);

        $manager->persist($testUserPublishedPost);
        $manager->flush();

        $anotherTestUserPublishedPost = (new PostBuilder())
            ->published()
            ->withAuthorId(new AuthorId($testUser->getId()->getValue()))
            ->withTitle('Published Test Title 2')
            ->withPreviewText('Published Test Preview Text 2')
            ->withText('Published Test Text 2')
            ->build();

        $manager->persist($anotherTestUserPublishedPost);
        $manager->flush();

        $testUserDraftPost = (new PostBuilder())
            ->draft()
            ->withAuthorId(new AuthorId($testUser->getId()->getValue()))
            ->withTitle('Draft Test Title')
            ->withPreviewText('Draft Test Preview Text')
            ->withText('Draft Test Text')
            ->build();

        $manager->persist($testUserDraftPost);
        $manager->flush();
    }

    /**
     * @return array|string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            CommonUserFixture::class,
        ];
    }
}
