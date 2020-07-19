<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Reset\Reset;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Forms\Reset\ResetForm;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\Forms\FormTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class ResetTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/reset';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    private FormTestCaseHelper $form;

    private ResetForm $resetForm;

    public function __construct()
    {
        parent::__construct();

        $this->url       = new UrlTestCaseHelper($this);
        $this->alert     = new AlertTestCaseHelper($this);
        $this->resetForm = new ResetForm();
        $this->form      = new FormTestCaseHelper($this, $this->resetForm);
    }

    /**
     * Открытие формы с несуществующим токеном
     */
    public function testNotExistedToken()
    {
        $crawler = $this->url->get(self::BASE_URL . '/123', true);
        $this->assertResponseIsSuccessful();

        $this->url->assertCurrentUri();

        $this->alert->assertDangerAlertContains('Неправильный или уже подтвержденный токен.', $crawler);
    }

    /**
     * Отображение страницы с формой аутентификации для гостей с токеном сброса пароля
     */
    public function testShowFormGuest()
    {
        $this->loadFixtures([ResetFixture::class]);

        $crawler = $this->url->get(self::BASE_URL . '/123');
        $this->assertResponseIsSuccessful();

        $this->assertContains('Новый пароль', $crawler->filter('h1')->text());

        $this->form->assertInputsExists($crawler);
    }

    /**
     * Отображение страницы с формой аутентификации для аутентифицированных пользователей с токеном сброса пароля
     */
    public function testShowFormAuth()
    {
        $this->loadFixtures([ResetFixture::class, UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL . '/123');

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Установка неправильного пароля
     */
    public function testNotValidPassword()
    {
        $this->loadFixtures([ResetFixture::class]);

        $this->url->get(self::BASE_URL . '/123');

        $crawler = $this->form->submit->notValid();

        $this->form->validation->assertShortPasswordErrorMessage(ResetForm::FIELD_PASSWORD, $crawler);
    }

    /**
     * Попытка подтверждения истекшего токена
     */
    public function testExpiredToken()
    {
        $this->loadFixtures([ResetFixture::class]);

        $this->url->get(self::BASE_URL . '/456');

        $crawler = $this->form->submit->success();

        $this->alert->assertDangerAlertContains('Токен сброса пароля уже истек.', $crawler);
    }

    /**
     * Успешный сброс пароля
     */
    public function testSuccess()
    {
        $this->loadFixtures([ResetFixture::class]);

        $this->url->get(self::BASE_URL . '/123');

        $crawler = $this->form->submit->success(true);
        $this->url->assertCurrentUri();
        $this->alert->assertSuccessAlertContains('Пароль успешно изменен.', $crawler);
    }
}
