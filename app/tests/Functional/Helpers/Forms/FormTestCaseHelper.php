<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers\Forms;

use App\Tests\Functional\Forms\AbstractForm;
use App\Tests\Functional\Helpers\AbstractBaseTestCaseHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class FormTestCaseHelper extends AbstractBaseTestCaseHelper
{
    private AbstractForm $form;

    public ValidationTestCaseHelper $validation;

    public SubmitTestCaseHelper $submit;

    /**
     * @param WebTestCase  $testCase
     * @param AbstractForm $form
     */
    public function __construct(WebTestCase $testCase, AbstractForm $form)
    {
        parent::__construct($testCase);

        $this->form = $form;
        $this->validation = new ValidationTestCaseHelper($testCase, $form);
        $this->submit = new SubmitTestCaseHelper($testCase, $form);
    }

    /**
     * Проверка - все поля формы присутствуют в ответе
     *
     * @param Crawler $crawler
     */
    public function assertInputsExists(Crawler $crawler)
    {
        $form   = $this->form;
        $fields = $form->getFields();

        foreach ($fields as $field) {
            $fieldTemplate = 'input[id="' . $form->formName . '_' . $field . '"]';

            $this->testCase->assertCount(1, $crawler->filter($fieldTemplate), 'В форме не найдено поле - ' . $field);
        }
    }
}
