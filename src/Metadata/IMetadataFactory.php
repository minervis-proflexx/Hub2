<?php

namespace srag\Plugins\Hub2\Metadata;

/**
 * Class IMetadataFactory
 * @package srag\Plugins\Hub2\Metadata
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
interface IMetadataFactory
{

    /**
     * @param int $ilias_field_id
     * @param int $record_id
     * @return IMetadata
     */
    public function getDTOWithIliasId(int $ilias_field_id, int $record_id = IMetadata::DEFAULT_RECORD_ID) : IMetadata;
}
