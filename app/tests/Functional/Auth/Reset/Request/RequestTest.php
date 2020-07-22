<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Reset\Request;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Forms\Reset\RequestForm;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\Forms\FormTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class RequestTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/reset';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    private FormTestCaseHelper $form;

    private RequestForm $requestForm;


    public function __construct()
    {
        parent::__construct();

        $this->url         = new UrlTestCaseHelper($this);
        $this->alert       = new AlertTestCaseHelper($this);
        $this->requestForm = new RequestForm();
        $this->form        = new FormTestCaseHelper($this, $this->requestForm);
    }

    /**
     * Отображение страницы с формой аутентификации для гостей
     */
    public function testShowFormGuest()
    {
        $crawler = $this->url->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();

        $this->assertContains('Сброс пароля', $crawler->filter('h1')->text());

        $this->form->assertInputsExists($crawler);
    }

    /**
     * Отображение страницы с формой аутентификации для аутентифицированных пользователей
     */
    public function testShowFormAuth()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Заполнен некорректный email
     */
    public function testNotValidEmail()
    {
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->notValid();

        $this->form->validation->assertEmailErrorMessage(RequestForm::FIELD_EMAIL, $crawler);
    }

    /**
     * Заполнен несуществующий email
     */
    public function testNotExistingEmail()
    {
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->submit($this->requestForm->getNotExistingData());
        $this->alert->assertDangerAlertContains('Пользователь не найден.', $crawler);
    }

    /**
     * Email пользователя не подтверждён
     */
    public function testNotConfirmedEmail()
    {
        $this->loadFixtures([RequestFixture::class]);

        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->submit($this->requestForm->getNotConfirmedData());
        $this->alert->assertDangerAlertContains('Пользователь ещё не активен.', $crawler);
    }

    /**
     * Сброс пароля уже запрошен
     */
    public function testAlreadyRequestedReset()
    {
        $this->loadFixtures([RequestFixture::class]);

        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->submit($this->requestForm->getAlreadyRequestData());
        $this->alert->assertDangerAlertContains('Сброс пароля уже запрошен.', $crawler);
    }

    /**
     * Успешный запрос смены пароля
     */
    public function testSuccess()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->success(true);
        $this->url->assertCurrentUri();
        $this->alert->assertSuccessAlertContains('Проверьте ваш email.', $crawler);
    }
}
