<?php

namespace srag\Plugins\Hub2\Sync\Processor\OrgUnitMembership;

use srag\Plugins\Hub2\Sync\Processor\FakeIliasMembershipObject;

/**
 * Class FakeOrgUnitMembershipObject
 * @package srag\Plugins\Hub2\Sync\Processor\OrgUnitMembership
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FakeOrgUnitMembershipObject extends FakeIliasMembershipObject
{
    /**
     * @var int
     */
    protected $position_id;

    /**
     * @param int $container_id_ilias
     * @param int $user_id_ilias
     * @param int $position_id
     */
    public function __construct(int $container_id_ilias, int $user_id_ilias, int $position_id)
    {
        parent::__construct($container_id_ilias, $user_id_ilias);

        $this->position_id = $position_id;

        $this->initId();
    }

    /**
     * @return int
     */
    public function getPositionId(): int
    {
        return $this->position_id;
    }

    /**
     * @param int $position_id
     */
    public function setPositionId(int $position_id)
    {
        $this->position_id = $position_id;
    }

    /**
     *
     */
    public function initId()
    {
        $this->setId(implode(self::GLUE, [$this->container_id_ilias, $this->user_id_ilias, $this->position_id]));
    }

    /**
     * @param string $id
     * @return FakeOrgUnitMembershipObject
     */
    public static function loadInstanceWithConcatenatedId(string $id)
    {
        [$container_id_ilias, $user_id_ilias, $position_id] = explode(self::GLUE, $id);

        return new self((int) $container_id_ilias, (int) $user_id_ilias, (int) $position_id);
    }
}
