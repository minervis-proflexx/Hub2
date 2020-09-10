<?php

namespace srag\Plugins\Hub2\Metadata;

/**
 * Interface IMetadata
 *
 * @package srag\Plugins\Hub2\Metadata
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
interface IMetadata
{

    const DEFAULT_RECORD_ID = 1;


    /**
     * @param string $value
     *
     * @return IMetadata
     */
    public function setValue($value) : IMetadata;


    /**
     * @param int $identifier
     *
     * @return IMetadata
     */
    public function setIdentifier(int $identifier) : IMetadata;


    /**
     * @return mixed
     */
    public function getValue();


    /**
     * @return mixed
     */
    public function getIdentifier();


    /**
     * @return int
     */
    public function getRecordId() : int;


    /**
     * @return string
     */
    public function __toString() : string;
}
