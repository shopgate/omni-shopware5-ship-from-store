<?php

namespace SgateShipFromStore\Components\Order\Serializer\Converter;

use Dustin\ImpEx\Util\Type;
use SgateShipFromStore\Components\Order\Encapsulation\LineItem;

class LineItemFilter
{
    private array $types;

    public function __construct(int ...$types)
    {
        $this->types = array_unique($types);
    }

    public function __invoke($lineItem): bool
    {
        return \in_array($this->getType($lineItem), $this->types);
    }

    protected function getType($lineItem): string
    {
        if ($lineItem instanceof LineItem) {
            return (string) $lineItem->get('type');
        }

        if (\is_array($lineItem)) {
            return $lineItem['type'] ?? '';
        }

        throw new \UnexpectedValueException(sprintf('Expected line item to be %s or array. Got %s.', LineItem::class, Type::getDebugType($lineItem)));
    }
}
