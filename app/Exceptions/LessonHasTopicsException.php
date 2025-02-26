<?php

namespace App\Exceptions;

use Exception;

class LessonHasTopicsException extends Exception
{
    public function __construct()
    {
        parent::__construct(trans('cannot_delete_because_data_is_associated_with_other_data'));
    }
}
