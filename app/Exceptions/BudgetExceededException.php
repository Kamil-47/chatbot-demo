<?php

namespace App\Exceptions;

use Exception;

class BudgetExceededException extends Exception
{
    public function __construct(private string $scope, string $message = '')
    {
        parent::__construct($message);
    }

    public function getScope(): string
    {
        return $this->scope;
    }
}
