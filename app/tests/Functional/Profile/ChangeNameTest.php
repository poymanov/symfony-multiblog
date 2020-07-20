<?php

declare(strict_types=1);

namespace Functional\Profile;

use App\DataFixtures\UserFixture;
use App\Tests\Functional\DbWebTestCase;
use App\Tests\Functional\Forms\Profile\ChangeNameForm;
use App\Tests\Functional\Helpers\AlertTestCaseHelper;
use App\Tests\Functional\Helpers\Forms\FormTestCaseHelper;
use App\Tests\Functional\Helpers\UrlTestCaseHelper;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class ChangeNameTest extends DbWebTestCase
{
    use FixturesTrait;

    private const BASE_URL = '/profile/name';

    private UrlTestCaseHelper $url;

    private AlertTestCaseHelper $alert;

    private FormTestCaseHelper $form;

    private ChangeNameForm $changeNameForm;

    public function __construct()
    {
        parent::__construct();

        $this->url   = new UrlTestCaseHelper($this);
        $this->alert = new AlertTestCaseHelper($this);
        $this->changeNameForm = new ChangeNameForm();
        $this->form  = new FormTestCaseHelper($this, $this->changeNameForm);
    }

    /**
     * Отображение страницы с формой изменения имени для гостей
     */
    public function testShowForm(): void
    {
        $crawler = $this->url->get(self::BASE_URL, true);
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

        $this->assertContains('Изменить имя', $crawler->filter('h1')->text());
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

        $crawler = $this->form->submit->submit($this->changeNameForm->getEmptyData());

        $this->form->validation->assertRequiredErrorMessage(ChangeNameForm::FIELD_FIRST, $crawler);
        $this->form->validation->assertRequiredErrorMessage(ChangeNameForm::FIELD_LAST, $crawler);
    }

    /**
     * Отправка формы с значениями больше допустимой длины
     */
    public function testLong()
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->submit($this->changeNameForm->getLongData());

        $this->form->validation->assertTooLongErrorMessage(ChangeNameForm::FIELD_FIRST, 255, $crawler);
        $this->form->validation->assertTooLongErrorMessage(ChangeNameForm::FIELD_LAST, 255, $crawler);
    }

    /**
     * Успешная изменение данных
     */
    public function testSuccess(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $this->client->setServerParameters(UserFixture::userCredentials());
        $this->url->get(self::BASE_URL);

        $crawler = $this->form->submit->success(true);
        $this->alert->assertSuccessAlertContains('Имя изменено.', $crawler);
    }
}
