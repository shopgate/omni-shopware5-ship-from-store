<?php

namespace SgateShipFromStore\Components\Article\Encapsulation;

use Dustin\Encapsulation\ArrayEncapsulation;

class StockIdentifier extends ArrayEncapsulation
{
    public function getAllowedFields(): ?array
    {
        return ['productCode', 'shopId'];
    }

    public function toString(): string
    {
        return $this->get('productCode').'@'.$this->get('shopId');
    }
}
