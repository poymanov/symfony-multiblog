<?php

namespace App\Tests\Functional\Auth\SignUp\Request;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Forms\SignUp\Form;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\Forms\FormTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;

class RequestTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/signup';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    private FormTestCaseHelper $form;


    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
        $this->alert = new AlertTestCaseHelper($this);
        $this->form  = new FormTestCaseHelper($this, new Form());
    }

    /**
     * Отображение страницы с формой регистрации
     */
    public function testShowForm(): void
    {
        $crawler = $this->url->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();

        $this->assertContains('Регистрация', $crawler->filter('h1')->text());
        $this->assertContains('Facebook', $crawler->filter('body')->text());

        $this->form->assertInputsExists($crawler);
    }

    /**
     * Отображение страницы с формой регистрации для аутентифицированных пользователей
     */
    public function testShowFormAuth()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Успешная регистрация
     */
    public function testSuccess(): void
    {
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->success(true);
        $this->alert->assertSuccessAlertContains('Проверьте ваш email.', $crawler);
    }

    /**
     * Заполнены не все регистрационные данные
     */
    public function testNotValid(): void
    {
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->notValid();

        $this->form->validation->assertRequiredErrorMessage(Form::FIELD_FIRST_NAME, $crawler);
        $this->form->validation->assertRequiredErrorMessage(Form::FIELD_LAST_NAME, $crawler);
        $this->form->validation->assertEmailErrorMessage(Form::FIELD_EMAIL, $crawler);
        $this->form->validation->assertShortPasswordErrorMessage(Form::FIELD_PASSWORD, $crawler);
    }

    /**
     * Email уже существует
     */
    public function testExists(): void
    {
        $this->loadFixtures([RequestFixture::class]);
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->existing();

        $this->alert->assertDangerAlertContains('Пользователь уже существует.', $crawler);
    }
}
