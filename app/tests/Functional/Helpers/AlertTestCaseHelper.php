<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers;

use Symfony\Component\DomCrawler\Crawler;

class AlertTestCaseHelper extends AbstractBaseTestCaseHelper
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
        $this->testCase->assertContains($text, $crawler->filter('.alert-' . $type)->text());
    }
}
