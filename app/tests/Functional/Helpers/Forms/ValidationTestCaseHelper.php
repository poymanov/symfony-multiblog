<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers\Forms;

use App\Tests\Functional\Forms\AbstractForm;
use App\Tests\Functional\Helpers\AbstractBaseTestCaseHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ValidationTestCaseHelper extends AbstractBaseTestCaseHelper
{
    private AbstractForm $form;

    /**
     * @param WebTestCase  $testCase
     * @param AbstractForm $form
     */
    public function __construct(WebTestCase $testCase, AbstractForm $form)
    {
        parent::__construct($testCase);

        $this->form = $form;
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
     * @param AbstractForm $form
     * @param string       $field
     * @param Crawler      $crawler
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
        $fieldId           = '#' . $this->form->formName . '_' . $field;
        $errorMessageClass = '.form-error-message';

        $this->testCase->assertContains($message, $crawler->filter($fieldId)->parents()->first()->filter($errorMessageClass)->text());
    }
}
