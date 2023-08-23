<?php

namespace SgateShipFromStore\Components\Customer;

use Doctrine\DBAL\Connection;
use Dustin\Encapsulation\ArrayEncapsulation;
use Dustin\ImpEx\Serializer\Normalizer\EncapsulationNormalizer;
use SgateShipFromStore\Framework\Reader\DbalReader;
use SgateShipFromStore\Framework\Util\ArrayUtil;

class CustomerEmailReader extends DbalReader
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
        $sql = 'SELECT `customer`.`id` as `id`, `customer`.`language` as `shopId`
            FROM `s_user` `customer` 
            LEFT JOIN `s_user_attributes` `customer_attribute` ON `customer`.`id` = `customer_attribute`.`userID`
            WHERE (`customer_attribute`.`sgate_ship_from_store_customer_number` != 0 
                AND `customer_attribute`.`sgate_ship_from_store_customer_number` IS NOT NULL
                AND `customer_attribute`.`sgate_ship_from_store_customer_exported` = 0) 
            ORDER BY `customer`.`language`, `customer`.`changed`';

        $customers = $this->connection->executeQuery($sql)->fetchAll();

        yield from array_column($customers, 'id');
    }

    protected function read($id): array
    {
        $data = $this->fetchCustomer($id);

        return $data;
    }

    protected function fetchCustomer(int $id): array
    {
        $data = $this->connection->createQueryBuilder()
            ->select([
                '`customer`.`id` as `shopwareId`',
                '`customernumber` as `externalCustomerNumber`',
                '`customer`.`email` as `emailAddress`',
                '`customer`.`language` as `shopId`',
                '`customer_attribute`.`sgate_ship_from_store_customer_number` as `internalCustomerNumber`',
            ])
            ->from('`s_user`', '`customer`')
            ->where('`customer`.`id` = :id')
            ->leftJoin('`customer`', '`s_user_attributes`', '`customer_attribute`', '`customer`.`id` = `customer_attribute`.`userID`')
            ->setParameter('id', $id)
            ->execute()->fetch();

        $data['shopCode'] = $this->config->get($data['shopId'])->get('shopCode');

        $data = ArrayUtil::flatToNested($data);

        return $data;
    }
}
