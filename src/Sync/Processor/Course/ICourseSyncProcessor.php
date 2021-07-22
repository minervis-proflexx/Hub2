<?php

namespace srag\Plugins\Hub2\Sync\Processor\Course;

use srag\Plugins\Hub2\Sync\IDidacticTemplateSyncProcessor;
use srag\Plugins\Hub2\Sync\Processor\IMetadataSyncProcessor;
use srag\Plugins\Hub2\Sync\Processor\IObjectSyncProcessor;
use srag\Plugins\Hub2\Sync\Processor\ITaxonomySyncProcessor;

/**
 * Interface ICourseSyncProcessor
 * @package srag\Plugins\Hub2\Sync\Processor\Course
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
interface ICourseSyncProcessor extends IObjectSyncProcessor, IMetadataSyncProcessor, ITaxonomySyncProcessor, IDidacticTemplateSyncProcessor
{

}
