<?php

declare(strict_types=1);

namespace App\Tests\Functional\Profile\Email\Request;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Forms\Profile\ChangeEmailForm;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\Forms\FormTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class RequestTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/profile/email';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    private FormTestCaseHelper $form;

    private ChangeEmailForm $changeEmailForm;

    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
        $this->alert = new AlertTestCaseHelper($this);
        $this->changeEmailForm = new ChangeEmailForm();
        $this->form  = new FormTestCaseHelper($this, $this->changeEmailForm);
    }

    /**
     * Отображение страницы с формой изменения имени для гостей
     */
    public function testShowForm(): void
    {
        $this->url->get(self::BASE_URL, true);
        $this->url->assertCurrentUri('login');
    }

    /**
     * Отображение страницы с формой изменения имени для аутентифицированных пользователей
     */
    public function testShowFormAuth()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $crawler = $this->url->get(self::BASE_URL);

        $this->assertContains('Изменить email', $crawler->filter('h1')->text());
        $this->form->assertInputsExists($crawler);
    }

    /**
     * Отправка формы с пустыми значениями
     */
    public function testEmpty()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->submit($this->changeEmailForm->getEmptyData());

        $this->form->validation->assertRequiredErrorMessage(ChangeEmailForm::FIELD_EMAIL, $crawler);
    }

    /**
     * Указан некорректный email
     */
    public function testNotValid(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->notValid();

        $this->form->validation->assertEmailErrorMessage(ChangeEmailForm::FIELD_EMAIL, $crawler);
    }

    /**
     * Email уже существует
     */
    public function testExists(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->existing();

        $this->alert->assertDangerAlertContains('Email уже используется.', $crawler);
    }

    /**
     * Успешный запрос на изменение email
     */
    public function testSuccess(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->success(true);

        $this->url->assertCurrentUri('profile');

        $this->alert->assertSuccessAlertContains('Проверьте ваш email.', $crawler);
    }
}
