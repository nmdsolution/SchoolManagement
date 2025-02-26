<?php


namespace App\Yadiko\Student\Domain\Eception;


class StudentTransferException extends \Exception
{
    protected $context;

    public function __construct($message, $context = [], $code = 0, \Throwable $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext()
    {
        return $this->context;
    }
}