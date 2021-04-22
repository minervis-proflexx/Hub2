<?php

namespace srag\Plugins\Hub2\Sync\Processor\Group;

use srag\Plugins\Hub2\Sync\Processor\IMetadataSyncProcessor;
use srag\Plugins\Hub2\Sync\Processor\IObjectSyncProcessor;
use srag\Plugins\Hub2\Sync\Processor\ITaxonomySyncProcessor;
use srag\Plugins\Hub2\Sync\IDidacticTemplateSyncProcessor;

/**
 * Interface IGroupSyncProcessor
 *
 * @package srag\Plugins\Hub2\Sync\Processor\Group
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
interface IGroupSyncProcessor extends IObjectSyncProcessor, IMetadataSyncProcessor, ITaxonomySyncProcessor, IDidacticTemplateSyncProcessor
{

}
