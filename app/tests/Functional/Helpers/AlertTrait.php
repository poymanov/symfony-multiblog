<?php

namespace App\Tests\Functional\Helpers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

trait AlertTrait
{
    /**
     * Проверка наличия текста в уведомлении об успешной операции
     *
     * @param string  $text
     * @param Crawler $crawler
     */
    public function assertSuccessAlertContains(string $text, Crawler $crawler): void
    {
        $this->assertAlertContains('success', $text, $crawler);
    }

    /**
     * Проверка наличия текста в уведомлении об операции с ошибкой
     *
     * @param string  $text
     * @param Crawler $crawler
     */
    public function assertDangerAlertContains(string $text, Crawler $crawler): void
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
    public function assertAlertContains(string $type, string $text, Crawler $crawler): void
    {
        /** @var $testCase WebTestCase */
        $testCase = $this;

        $testCase->assertContains($text, $crawler->filter('.alert-' . $type)->text());
    }
}
