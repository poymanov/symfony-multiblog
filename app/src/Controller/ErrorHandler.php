<?php

declare(strict_types=1);

namespace App\Controller;

use DomainException;
use Psr\Log\LoggerInterface;

class ErrorHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param DomainException $e
     */
    public function handle(DomainException $e): void
    {
        $this->logger->warning($e->getMessage(), ['exception' => $e]);
    }
}
