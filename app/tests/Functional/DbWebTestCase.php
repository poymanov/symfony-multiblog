<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Functional\Helpers\AlertTrait;
use App\Tests\Functional\Helpers\AuthTrait;
use App\Tests\Functional\Helpers\DbTrait;
use App\Tests\Functional\Helpers\FormTrait;
use App\Tests\Functional\Helpers\UrlTrait;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class DbWebTestCase extends WebTestCase
{
    use UrlTrait, AlertTrait, FormTrait, AuthTrait, DbTrait;

    /**
     * @var EntityManagerInterface
     */
    public $em;

    /**
     * @var KernelBrowser
     */
    public $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->em->getConnection()->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    /**
     * @throws ConnectionException
     */
    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        $this->em->close();
        parent::tearDown();
    }

    /**
     * Получение содержимого страницы
     *
     * @param Crawler $crawler
     *
     * @return string
     */
    protected function getBodyText(Crawler $crawler): string
    {
        return $crawler->filter('body')->text();
    }
}
