<?php

namespace Dustin\ImpEx\ArrayEncapsulation;

class Config extends ArrayEncapsulation
{
    public function isConfigured(string $field): bool
    {
        $value = $this->get($field);

        // 0 and false are valid config values
        if (
            $value === false ||
            $value === 0
        ) {
            return true;
        }

        return !empty($value);
    }
}
