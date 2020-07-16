<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth\Login;

use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Forms\Login\Form;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\Forms\FormTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class LoginTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/login';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    private FormTestCaseHelper $form;

    private Form $loginForm;


    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
        $this->alert = new AlertTestCaseHelper($this);
        $this->loginForm = new Form();
        $this->form  = new FormTestCaseHelper($this, $this->loginForm);
    }

    /**
     * Отображение страницы с формой аутентификации
     */
    public function testShowForm()
    {
        $crawler = $this->url->get(self::BASE_URL);
        $this->assertResponseIsSuccessful();

        $this->assertContains('Войти', $crawler->filter('h1')->text());
        $this->assertContains('Facebook', $crawler->filter('body')->text());

        $this->form->assertInputsExists($crawler);
    }

    /**
     * Успешная аутентификация
     */
    public function testSuccess(): void
    {
        $this->loadFixtures([LoginFixture::class]);
        $this->url->get(self::BASE_URL);
        $this->form->submit->success(true);
        $this->url->assertCurrentUri();
    }

    /**
     * Пользователь не подтвержден
     */
    public function testNotConfirmed(): void
    {
        $this->loadFixtures([LoginFixture::class]);
        $this->url->get(self::BASE_URL);
        $crawler = $this->form->submit->submit($this->loginForm->getNotConfirmedData(), true);
        $this->url->assertCurrentUri('login');
        $this->alert->assertDangerAlertContains('Учетная запись отключена.', $crawler);
    }

    /**
     * Заполнен несуществующий email
     */
    public function testNotValidEmail(): void
    {
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->submit($this->loginForm->getNotExistedData(), true);

        $this->url->assertCurrentUri('login');
        $this->alert->assertDangerAlertContains('Имя пользователя не найдено.', $crawler);
    }

    /**
     * Заполнен некорректный email
     */
    public function testNotValidPassword(): void
    {
        $this->loadFixtures([LoginFixture::class]);

        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->notValid(true);

        $this->url->assertCurrentUri('login');
        $this->alert->assertDangerAlertContains('Недействительные аутентификационные данные.', $crawler);
    }
}
