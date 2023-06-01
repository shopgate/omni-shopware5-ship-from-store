<?php

namespace SgateShipFromStore\Components\Order\Serializer;

use Dustin\ImpEx\Serializer\Converter\ArrayList\Filter;
use Dustin\ImpEx\Serializer\Converter\ArrayList\ListConverter;
use Dustin\ImpEx\Serializer\Converter\AttributeConverterSequence;
use Dustin\ImpEx\Serializer\Converter\Bool\BoolConverter;
use Dustin\ImpEx\Serializer\Converter\DateTime\DateTimeConverter;
use Dustin\ImpEx\Serializer\Converter\NormalizerConverter;
use Dustin\ImpEx\Serializer\Converter\Numeric\FloatConverter;
use Dustin\ImpEx\Serializer\Converter\Numeric\IntConverter;
use SgateShipFromStore\Components\Customer\Encapsulation\Customer;
use SgateShipFromStore\Components\Customer\Serializer\CustomerNormalizer;
use SgateShipFromStore\Components\Order\Encapsulation\Address;
use SgateShipFromStore\Components\Order\Encapsulation\LineItem;
use SgateShipFromStore\Components\Order\Encapsulation\Order;
use SgateShipFromStore\Components\Order\Serializer\Converter\DiscountAmountCalculator;
use SgateShipFromStore\Components\Order\Serializer\Converter\LineItemFilter;
use SgateShipFromStore\Components\Order\Serializer\Converter\SubTotalCalculator;
use SgateShipFromStore\Framework\Serializer\EncapsulationNormalizer;

class OrderNormalizer extends EncapsulationNormalizer
{
    /**
     * @var DateTimeConverter
     */
    private $dateTimeConverter;

    public function __construct(string $metaFile, DateTimeConverter $dateTimeConverter)
    {
        $this->dateTimeConverter = $dateTimeConverter;

        parent::__construct($metaFile);
    }

    public function getEncapsulationClass(): ?string
    {
        return Order::class;
    }

    protected function createDefaultContext(): array
    {
        $addressNormalizer = new AddressNormalizer($this->metaFile);
        $lineItemNormalizer = new LineItemNormalizer($this->metaFile);
        $customerNormalizer = new CustomerNormalizer($this->metaFile);

        return [
            self::CONVERTERS => [
                'primaryBillToAddressSequenceIndex' => new IntConverter(),
                'primaryShipToAddressSequenceIndex' => new IntConverter(),
                'shippingTotal' => new FloatConverter(),
                'taxAmount' => new FloatConverter(),
                'total' => new FloatConverter(),
                'taxExempt' => new BoolConverter(),
                'submitDate' => $this->dateTimeConverter,
                'addressSequences' => new ListConverter(
                    new NormalizerConverter(
                        $addressNormalizer,
                        $addressNormalizer,
                        Address::class,
                        null,
                        $this
                    )
                ),
                'lineItems' => new AttributeConverterSequence(
                    new ListConverter(
                        new NormalizerConverter(
                            $lineItemNormalizer,
                            $lineItemNormalizer,
                            LineItem::class,
                            null,
                            $this
                        )
                    ),
                    new Filter(new LineItemFilter(0, 1))
                ),
                'discountAmount' => new DiscountAmountCalculator(),
                'subTotal' => new SubTotalCalculator(),
                'customer' => new NormalizerConverter(
                    $customerNormalizer,
                    $customerNormalizer,
                    Customer::class,
                    null,
                    $this
                ),
            ],
        ];
    }
}
