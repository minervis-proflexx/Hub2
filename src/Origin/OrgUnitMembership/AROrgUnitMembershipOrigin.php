<?php

namespace srag\Plugins\Hub2\Origin\OrgUnitMembership;

use srag\Plugins\Hub2\Origin\AROrigin;
use srag\Plugins\Hub2\Origin\Config\OrgUnitMembership\IOrgUnitMembershipOriginConfig;
use srag\Plugins\Hub2\Origin\Config\OrgUnitMembership\OrgUnitMembershipOriginConfig;
use srag\Plugins\Hub2\Origin\Properties\OrgUnitMembership\IOrgUnitMembershipProperties;
use srag\Plugins\Hub2\Origin\Properties\OrgUnitMembership\OrgUnitMembershipProperties;

/**
 * Class AROrgUnitMembershipOrigin
 * @package srag\Plugins\Hub2\Origin\OrgUnitMembership
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class AROrgUnitMembershipOrigin extends AROrigin implements IOrgUnitMembershipOrigin
{
    /**
     * @inheritdoc
     */
    protected function getOriginConfig(array $data): IOrgUnitMembershipOriginConfig
    {
        return new OrgUnitMembershipOriginConfig($data);
    }

    /**
     * @inheritdoc
     */
    protected function getOriginProperties(array $data): IOrgUnitMembershipProperties
    {
        return new OrgUnitMembershipProperties($data);
    }

    /**
     * @inheritdoc
     */
    public function config(): IOrgUnitMembershipOriginConfig
    {
        return parent::config();
    }

    /**
     * @inheritdoc
     */
    public function properties(): IOrgUnitMembershipProperties
    {
        return parent::properties();
    }
}
