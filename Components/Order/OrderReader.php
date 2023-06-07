<?php

namespace SgateShipFromStore\Components\Order;

use Doctrine\DBAL\Connection;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\ImpEx\Serializer\Normalizer\EncapsulationNormalizer;
use SgateShipFromStore\Framework\Reader\DbalReader;
use SgateShipFromStore\Framework\Util\ArrayUtil;

class OrderReader extends DbalReader
{
    /**
     * @var ArrayEncapsulation
     */
    private $config;

    public function __construct(
        Connection $connection,
        EncapsulationNormalizer $denormalizer,
        ArrayEncapsulation $config
    ) {
        $this->config = $config;

        parent::__construct($connection, $denormalizer);
    }

    public function getNextUpIdentifiers(): \Generator
    {
        $sql = 'SELECT `order`.`id` as `id`, `order`.`cleared` as `paymentStatusId`, `order`.`language` as `shopId`
            FROM `s_order` `order` 
            LEFT JOIN `s_order_attributes` `order_attribute` ON `order`.`id` = `order_attribute`.`orderID`
            WHERE (`order_attribute`.`sgate_ship_from_store_exported` = 0 OR `order_attribute`.`sgate_ship_from_store_exported` IS NULL) 
                AND `order`.`ordernumber` <> 0

            ORDER BY `order`.`language`, `order`.`ordertime`';

        $orders = $this->connection->executeQuery($sql)->fetchAll();

        $orders = array_filter($orders, function (array $order) {
            $statusId = $this->config->get($order['shopId'])->get('paymentStatusId');

            if ($statusId === null) {
                return true;
            }

            return (int) $order['paymentStatusId'] === (int) $statusId;
        });

        yield from array_column($orders, 'id');
    }

    protected function read($id): array
    {
        $data = $this->fetchOrder($id);
        $data['lineItems'] = $this->fetchLineItems($id);

        return $data;
    }

    protected function fetchOrder(int $id): array
    {
        $data = $this->connection->createQueryBuilder()
            ->select([
                '`order`.`id` as `id`',
                '`order`.`ordernumber` as `externalCode`',
                '`customer`.`customernumber` as `externalCustomerNumber`',
                'REPLACE(LOWER(`locale`.`locale`), "_", "-") as `localeCode`',
                '`order`.`currency` as `currencyCode`',
                '`order`.`taxfree` as `taxExempt`',
                'NULLIF(`order`.`customercomment`, "") as `notes`',
                'NULLIF(`order`.`internalcomment`, "") as `specialInstructions`',

                '"billing" as `addressSequences.0.type`',
                '`billing_address`.`firstname` as `addressSequences.0.firstName`',
                '`billing_address`.`lastname` as `addressSequences.0.lastName`',
                'NULLIF(`billing_address`.`company`, "") as `addressSequences.0.company`',
                '`billing_address`.`street` as `addressSequences.0.address1`',
                'NULLIF(`billing_address`.`additional_address_line1`, "") as `addressSequences.0.address2`',
                'NULLIF(`billing_address`.`additional_address_line2`, "") as `addressSequences.0.address3`',
                '`billing_address`.`city` as `addressSequences.0.city`',
                '`billing_address_state`.`shortcode` as `addressSequences.0.region`',
                '`billing_address`.`zipcode` as `addressSequences.0.postalCode`',
                '`billing_address_country`.`countryiso` as `addressSequences.0.country`',
                'NULLIF(`billing_address`.`phone`, "") as `addressSequences.0.phone`',
                '`customer`.`email` as `addressSequences.0.emailAddress`',

                '"shipping" as `addressSequences.1.type`',
                '`shipping_address`.`firstname` as `addressSequences.1.firstName`',
                '`shipping_address`.`lastname` as `addressSequences.1.lastName`',
                'NULLIF(`shipping_address`.`company`, "") as `addressSequences.1.company`',
                '`shipping_address`.`street` as `addressSequences.1.address1`',
                'NULLIF(`shipping_address`.`additional_address_line1`, "") as `addressSequences.1.address2`',
                'NULLIF(`shipping_address`.`additional_address_line2`, "") as `addressSequences.1.address3`',
                '`shipping_address`.`city` as `addressSequences.1.city`',
                '`shipping_address_state`.`shortcode` as `addressSequences.1.region`',
                '`shipping_address`.`zipcode` as `addressSequences.1.postalCode`',
                '`shipping_address_country`.`countryiso` as `addressSequences.1.country`',
                'NULLIF(`shipping_address`.`phone`, "") as `addressSequences.1.phone`',
                '`customer`.`email` as `addressSequences.1.emailAddress`',

                '"0" as `primaryBillToAddressSequenceIndex`',
                '"1" as `primaryShipToAddressSequenceIndex`',

                '`order`.`invoice_shipping` as `shippingTotal`',
                '`order`.`invoice_amount` as `total`',
                'CONCAT(DATE(`order`.`ordertime`), "T", TIME(`order`.`ordertime`), ".000Z") as `submitDate`',

                'IFNULL(`shop`.`host`, `main_shop`.`host`) as `domain`',
                '`shop`.`id` as `shopId`',
                '`order`.`invoice_amount` - `order`.`invoice_amount_net` as `taxAmount`',

                '`customer`.`customernumber` as `customer.externalCustomerNumber`',
                '`customer`.`email` as `customer.emailAddress`',
                '`customer`.`firstname` as `customer.firstName`',
                '`customer`.`lastname` as `customer.lastName`',
                '`customer_attribute`.`sgate_ship_from_store_customer_number` as `customer.internalCustomerNumber`',
                '`customer`.`id` as `customer.shopwareId`',
            ])
            ->from('`s_order`', '`order`')
            ->leftJoin('`order`', '`s_user`', '`customer`', '`order`.`userID` = `customer`.`id`')
            ->leftJoin('`order`', '`s_core_shops`', '`shop`', '`order`.`language` = `shop`.`id`')
            ->leftJoin('`shop`', '`s_core_shops`', '`main_shop`', '`shop`.`main_id` = `main_shop`.`id`')
            ->leftJoin('`shop`', '`s_core_locales`', '`locale`', '`shop`.`locale_id` = `locale`.`id`')
            ->leftJoin('`order`', '`s_order_billingaddress`', '`billing_address`', '`order`.`id` = `billing_address`.`orderID`')
            ->leftJoin('`billing_address`', '`s_core_countries_states`', '`billing_address_state`', '`billing_address`.`stateID` = `billing_address_state`.`id`')
            ->leftJoin('`billing_address`', '`s_core_countries`', '`billing_address_country`', '`billing_address`.`countryID` = `billing_address_country`.`id`')
            ->leftJoin('`order`', '`s_order_shippingaddress`', '`shipping_address`', '`order`.`id` = `shipping_address`.`orderID`')
            ->leftJoin('`shipping_address`', '`s_core_countries_states`', '`shipping_address_state`', '`shipping_address`.`stateID` = `shipping_address_state`.`id`')
            ->leftJoin('`shipping_address`', '`s_core_countries`', '`shipping_address_country`', '`shipping_address`.`countryID` = `shipping_address_country`.`id`')
            ->leftJoin('`customer`', '`s_user_attributes`', '`customer_attribute`', '`customer`.`id` = `customer_attribute`.`userID`')
            ->where('`order`.`id` = :id')
            ->setParameter('id', $id)
            ->execute()->fetch();

        $data['shopCode'] = $this->config->get($data['shopId'])->get('shopCode');

        $data = ArrayUtil::flatToNested($data);

        return $data;
    }

    protected function fetchLineItems(int $orderId): array
    {
        $data = $this->connection->createQueryBuilder()
            ->select([
                '`line_item`.`id` as `code`',
                '`line_item`.`quantity` as `quantity`',

                '`line_item`.`name` as `product.name`',
                'IF(
                    NULLIF(`article_price`.`pseudoprice`, 0) IS NULL,
                    `article_price`.`price` * (1+(`tax`.`tax` / 100)),
                    `article_price`.`pseudoprice` * (1+(`tax`.`tax` / 100))
                ) as `product.price`',
                'IF(
                    NULLIF(`article_price`.`pseudoprice`, 0) IS NOT NULL,
                    `article_price`.`price` * (1+(`tax`.`tax` / 100)),
                    NULL
                ) as `product.salePrice`',
                '`order`.`currency` as `product.currencyCode`',
                'NULLIF(`article_detail`.`ean`, "") as `product.identifiers.ean`',
                '`article_detail`.`ordernumber` as `product.identifiers.sku`',
                '`order`.`language` as `shopId`',
                'IF(
                    `order`.`net` = 1 AND `order`.`taxfree` = 0,
                    `line_item`.`price` * (1+(`line_item`.`tax_rate` / 100)),
                    `line_item`.`price`
                ) as `price`',
                'IF(
                    `order`.`net` = 1 AND `order`.`taxfree` = 0,
                    `line_item`.`price` * (1+(`line_item`.`tax_rate` / 100)),
                    `line_item`.`price`
                ) as `extendedPrice`',
                'IF(
                    `order`.`net` = 1,
                    IF(
                        `order`.`taxfree` = 0,
                        `line_item`.`price` * (`line_item`.`tax_rate` / 100),
                        0
                    ),
                    (`line_item`.`price` / (1+(`line_item`.`tax_rate` / 100))) * (`line_item`.`tax_rate` / 100)
                ) as `taxAmount`',
                '`line_item`.`modus` as `type`',
                '`order`.`currency` as `currencyCode`',
            ])
            ->from('`s_order_details`', '`line_item`')
            ->leftJoin('`line_item`', '`s_articles_prices`', '`article_price`', '`line_item`.`articleDetailID` = `article_price`.`articledetailsID`')
            ->leftJoin('`line_item`', '`s_articles`', '`article`', '`line_item`.`articleID` = `article`.`id`')
            ->leftJoin('`line_item`', '`s_articles_details`', '`article_detail`', '`line_item`.`articleDetailID` = `article_detail`.`id`')
            ->leftJoin('`article`', '`s_core_tax`', '`tax`', '`article`.`taxID` = `tax`.`id`')
            ->leftJoin('`line_item`', '`s_order`', '`order`', '`line_item`.`orderID` = `order`.`id`')
            ->leftJoin('`order`', '`s_core_shops`', '`shop`', '`order`.`language` = `shop`.`id`')
            ->leftJoin('`shop`', '`s_core_customergroups`', '`shop_customer_group`', '`shop`.`customer_group_id` = `shop_customer_group`.`id`')
            ->where('`line_item`.`orderID` = :orderId')
            ->andWhere('`article_price`.`pricegroup` = `shop_customer_group`.`groupkey` OR `line_item`.`modus` <> 0')
            ->andWhere('`article_price`.`from` = 1 OR `line_item`.`modus` <> 0')
            ->setParameter('orderId', $orderId)
            ->execute()->fetchAll();

        foreach ($data as &$lineItem) {
            $lineItem = ArrayUtil::flatToNested($lineItem);

            $config = $this->config->get($lineItem['shopId']);
            $lineItem['product']['code'] = $lineItem['product']['identifiers'][$config->get('productCode')];
            unset($lineItem['shopId']);
        }

        return $data;
    }
}
