<?php

declare(strict_types=1);

namespace App\Tests\Functional\Helpers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractBaseTestCaseHelper
{
    /**
     * @var WebTestCase
     */
    protected WebTestCase $testCase;

    /**
     * @param WebTestCase $testCase
     */
    public function __construct(WebTestCase $testCase)
    {
        $this->testCase = $testCase;
    }
}
