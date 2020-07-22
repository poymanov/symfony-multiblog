<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

trait FormTrait
{
    /**
     * Проверка - все поля формы присутствуют в ответе
     *
     * @param string  $field
     * @param Crawler $crawler
     */
    public function assertInputExists(string $field, Crawler $crawler)
    {
        /** @var $testCase WebTestCase */
        $testCase = $this;

        $testCase->assertCount(1, $crawler->filter($field), 'В форме не найдено поле - ' . $field);
    }

    /**
     * Отправка формы с данными
     *
     * @param FormDataDto $formData
     * @param bool        $followRedirect
     *
     * @return Crawler
     */
    public function submit(FormDataDto $formData, bool $followRedirect = false): Crawler
    {
        /** @var $testCase WebTestCase */
        $testCase = $this;

        $client = $testCase->client;

        $crawler = $client->submitForm($formData->getSubmitButtonTitle(), $formData->getData());

        if ($followRedirect) {
            $crawler = $client->followRedirect();
        }

        $testCase->assertResponseIsSuccessful();

        return $crawler;
    }

    /**
     * Проверка наличия сообщения о слишком длинном значении
     *
     * @param string  $field
     * @param int     $maxLength
     * @param Crawler $crawler
     */
    public function assertTooLongErrorMessage(string $field, int $maxLength, Crawler $crawler)
    {
        $errorMessage = 'Значение слишком длинное. Должно быть равно ' . $maxLength . ' символам или меньше.';
        $this->assertFormErrorMessage($field, $errorMessage, $crawler);
    }

    /**
     * Проверка наличия сообщения о незаполненном поле
     *
     * @param string  $field
     * @param Crawler $crawler
     */
    public function assertRequiredErrorMessage(string $field, Crawler $crawler)
    {
        $errorMessage = 'Значение не должно быть пустым.';
        $this->assertFormErrorMessage($field, $errorMessage, $crawler);
    }

    /**
     * Проверка наличия сообщения о неправильном email
     *
     * @param string  $field
     * @param Crawler $crawler
     */
    public function assertEmailErrorMessage(string $field, Crawler $crawler)
    {
        $errorMessage = 'Значение адреса электронной почты недопустимо.';
        $this->assertFormErrorMessage($field, $errorMessage, $crawler);
    }

    /**
     * Проверка наличия сообщения о коротком email
     *
     * @param string  $field
     * @param Crawler $crawler
     */
    public function assertShortPasswordErrorMessage(string $field, Crawler $crawler)
    {
        $errorMessage = 'Значение слишком короткое. Должно быть равно 6 символам или больше.';
        $this->assertFormErrorMessage($field, $errorMessage, $crawler);
    }

    /**
     * Проверка наличия ошибок в форме
     *
     * @param string  $field
     * @param string  $message
     * @param Crawler $crawler
     */
    private function assertFormErrorMessage(string $field, string $message, Crawler $crawler)
    {
        /** @var $testCase WebTestCase */
        $testCase = $this;

        $errorMessageClass = '.form-error-message';

        $testCase->assertContains($message, $crawler->filter($field)->parents()->first()->filter($errorMessageClass)->text());
    }
}
