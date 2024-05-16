<?php

namespace SgateShipFromStore\Framework\Reader;

use Doctrine\DBAL\Connection;
use Dustin\ImpEx\Serializer\Normalizer\EncapsulationNormalizer;

abstract class DbalReader extends Reader
{
    protected Connection $connection;

    public function __construct(
        Connection $connection,
        EncapsulationNormalizer $denormalizer
    ) {
        $this->connection = $connection;

        parent::__construct($denormalizer);
    }
}
