<?php

namespace srag\Plugins\Hub2\Sync;

use srag\Plugins\Hub2\Object\DTO\IDataTransferObject;

/**
 * Interface IDataTransferObjectSort
 * @package srag\Plugins\Hub2\Sync
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface IDataTransferObjectSort
{

    /**
     * @var int
     */
    const MAX_LEVEL = 100;

    /**
     * @return IDataTransferObject
     */
    public function getDtoObject() : IDataTransferObject;

    /**
     * @return int
     */
    public function getLevel() : int;

    /**
     * @param int $level
     */
    public function setLevel(int $level);
}
