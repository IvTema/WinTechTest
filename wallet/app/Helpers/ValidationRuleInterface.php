<?php

namespace App\Helpers;

interface ValidationRuleInterface
{
    public function getRules(): array;
}