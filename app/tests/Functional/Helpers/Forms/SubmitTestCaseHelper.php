<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers\Forms;

use App\Tests\Functional\Forms\AbstractForm;
use App\Tests\Functional\Helpers\AbstractBaseTestCaseHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class SubmitTestCaseHelper extends AbstractBaseTestCaseHelper
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
     * Отправка формы с корректными данными
     *
     * @param bool $followRedirect
     *
     * @return Crawler
     */
    public function success(bool $followRedirect = false)
    {
        return $this->submit($this->form->getCorrectData(), $followRedirect);
    }

    /**
     * Отправка формы с неправильными данными
     *
     * @param bool $followRedirect
     *
     * @return Crawler
     */
    public function notValid(bool $followRedirect = false)
    {
        return $this->submit($this->form->getNotValidData(), $followRedirect);
    }

    /**
     * Отправка формы с существующими данными
     *
     * @param bool $followRedirect
     *
     * @return Crawler
     */
    public function existing(bool $followRedirect = false)
    {
        return $this->submit($this->form->getExistingData(), $followRedirect);
    }

    /**
     * Отправка формы с данными
     *
     * @param array $data
     *
     * @param bool  $followRedirect
     *
     * @return Crawler
     */
    public function submit(array $data, bool $followRedirect = false): Crawler
    {
        $client = $this->testCase->client;

        $crawler = $client->submitForm($this->form->submitButtonTitle, $data);

        if ($followRedirect) {
            $crawler = $client->followRedirect();
        }

        $this->testCase->assertResponseIsSuccessful();

        return $crawler;
    }
}
