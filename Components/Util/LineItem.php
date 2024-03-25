<?php

namespace SgateShipFromStore\Components\Util;

class LineItem
{
    public static function isProductLineItem(array $lineItem): bool
    {
        $type = $lineItem['type'] ?? false;

        if ($type === false) {
            return false;
        }

        return \in_array($type, [0, 1]);
    }
}
