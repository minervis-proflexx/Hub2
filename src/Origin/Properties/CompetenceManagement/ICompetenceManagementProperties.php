<?php

namespace srag\Plugins\Hub2\Origin\Properties\CompetenceManagement;

use srag\Plugins\Hub2\Origin\Properties\IOriginProperties;

/**
 * Interface ICompetenceManagementProperties
 *
 * @package srag\Plugins\Hub2\Origin\Properties\CompetenceManagement
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface ICompetenceManagementProperties extends IOriginProperties
{

    /**
     * @var string
     */
    const MOVE = "move";
    /**
     * @var string
     */
    const DELETE_MODE = "delete_mode";
    /**
     * @var int
     */
    const DELETE_MODE_NONE = 0;
    /**
     * @var int
     */
    const DELETE_MODE_DELETE = 1;
    /**
     * @var string
     */
    const PROP_DESCRIPTION = "description";
    /**
     * @var string
     */
    const PROP_EXT_ID = "ext_id";
    /**
     * @var string
     */
    const PROP_PARENT_ID = "parent_id";
    /**
     * @var string
     */
    const PROP_PARENT_ID_TYPE = "parent_id_type";
    /**
     * @var string
     */
    const PROP_PROFILE_LEVELS = "profile_levels";
    /**
     * @var string
     */
    const PROP_PROFILE_ASSIGNED_USERS = "profile_assigned_users";
    /**
     * @var string
     */
    const PROP_SELF_EVALUATION = "self_evaluation";
    /**
     * @var string
     */
    const PROP_SKILL_LEVELS = "skill_levels";
    /**
     * @var string
     */
    const PROP_STATUS = "status";
    /**
     * @var string
     */
    const PROP_TITLE = "title";
    /**
     * @var string
     */
    const PROP_TYPE = "type";
}
