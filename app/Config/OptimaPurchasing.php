<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class OptimaPurchasing extends BaseConfig
{
    /**
     * When true, delivery cannot be set to Received until every unit
     * on that delivery has serial_number_po filled on po_units.
     */
    public bool $requireSerialBeforeReceived = false;
}
