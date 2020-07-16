<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class DbWebTestCase extends WebTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

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
     * Проверка наличия текста в уведомлении об успешной операции
     *
     * @param string  $text
     * @param Crawler $crawler
     */
    protected function assertSuccessAlertContains(string $text, Crawler $crawler): void
    {
        $this->assertAlertContains('success', $text, $crawler);
    }

    /**
     * Проверка наличия текста в уведомлении об операции с ошибкой
     *
     * @param string  $text
     * @param Crawler $crawler
     */
    protected function assertDangerAlertContains(string $text, Crawler $crawler): void
    {
        $this->assertAlertContains('danger', $text, $crawler);
    }

    /**
     * Проверка наличия текста в уведомлении по типу
     *
     * @param string  $type
     * @param string  $text
     * @param Crawler $crawler
     */
    protected function assertAlertContains(string $type, string $text, Crawler $crawler): void
    {
        $this->assertContains($text, $crawler->filter('.alert-' . $type)->text());
    }
}
