<?php

namespace App\Helpers;

class ValidationIndexRuleHelper implements ValidationRuleInterface
{
    public function getRules(): array
    {
        return [
            'id' => 'required|integer|min:1',
        ];
    }
}