<?php

namespace Therour\Actionable\Tests\Params;

use Therour\Actionable\Params\AbstractParam;

class MessageParam extends AbstractParam
{
    private $message;

    public function getMessage()
    {
        return strtoupper($this->message);
    }

    public static function rules()
    {
        return [
            'message' => 'required'
        ];
    }
}
