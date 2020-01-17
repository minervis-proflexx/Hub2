<#1>
<?php

\srag\Plugins\Hub2\Origin\User\ARUserOrigin::updateDB();
\srag\Plugins\Hub2\Object\User\ARUser::updateDB();
\srag\Plugins\Hub2\Object\Course\ARCourse::updateDB();
\srag\Plugins\Hub2\Object\CourseMembership\ARCourseMembership::updateDB();
\srag\Plugins\Hub2\Object\Category\ARCategory::updateDB();
\srag\Plugins\Hub2\Object\Session\ARSession::updateDB();
\srag\Plugins\Hub2\Object\Group\ARGroup::updateDB();
\srag\Plugins\Hub2\Object\GroupMembership\ARGroupMembership::updateDB();
\srag\Plugins\Hub2\Object\SessionMembership\ARSessionMembership::updateDB();
?>
<#2>
<?php
\srag\DIC\Hub2\DICStatic::dic()->database()
	->modifyTableColumn(\srag\Plugins\Hub2\Object\CourseMembership\ARCourseMembership::TABLE_NAME, 'ilias_id', array(
		"type" => "text",
		"length" => 256,
	));
\srag\DIC\Hub2\DICStatic::dic()->database()
	->modifyTableColumn(\srag\Plugins\Hub2\Object\SessionMembership\ARSessionMembership::TABLE_NAME, 'ilias_id', array(
		"type" => "text",
		"length" => 256,
	));
\srag\DIC\Hub2\DICStatic::dic()->database()
	->modifyTableColumn(\srag\Plugins\Hub2\Object\GroupMembership\ARGroupMembership::TABLE_NAME, 'ilias_id', array(
		"type" => "text",
		"length" => 256,
	));
?>
<#3>
<?php
\srag\Plugins\Hub2\Object\OrgUnit\AROrgUnit::updateDB();
\srag\Plugins\Hub2\Object\OrgUnitMembership\AROrgUnitMembership::updateDB();
?>
<#4>
<?php
\srag\Plugins\Hub2\Config\ArConfig::updateDB();

if (\srag\DIC\Hub2\DICStatic::dic()->database()->tableExists(\srag\Plugins\Hub2\Config\ArConfigOld::TABLE_NAME)) {
	\srag\Plugins\Hub2\Config\ArConfigOld::updateDB();

	foreach (\srag\Plugins\Hub2\Config\ArConfigOld::get() as $config) {
		/**
		 * @var \srag\Plugins\Hub2\Config\ArConfigOld $config
		 */
		switch ($config->getKey()) {
            case 0:
                // some installations seem to have an empty record with the key 0
                break;
			default:
				\srag\Plugins\Hub2\Config\ArConfig::setField(strval($config->getKey()), $config->getValue());
				break;
		}
	}

	\srag\DIC\Hub2\DICStatic::dic()->database()->dropTable(\srag\Plugins\Hub2\Config\ArConfigOld::TABLE_NAME);
}
?>
<#5>
<?php
$administration_role_ids = json_encode(\srag\Plugins\Hub2\Config\ArConfig::getField(\srag\Plugins\Hub2\Config\ArConfig::KEY_ADMINISTRATE_HUB_ROLE_IDS));

if (strpos($administration_role_ids, "[") === false) {
	$administration_role_ids = preg_split('/, */', $administration_role_ids);
	$administration_role_ids = array_map(function (string $id): int {
		return intval($id);
	}, $administration_role_ids);

	\srag\Plugins\Hub2\Config\ArConfig::setField(\srag\Plugins\Hub2\Config\ArConfig::KEY_ADMINISTRATE_HUB_ROLE_IDS, $administration_role_ids);
}
?>
<#6>
<?php
/* */
?>
<#7>
<?php
/* */
?>
<#8>
<?php
try {
    \srag\Plugins\Hub2\Log\Log::updateDB();
} catch (\Throwable $ex) {
    // Fix Call to a member function getName() on null (Because not use ILIAS sequence)
}
?>
<#9>
<?php
\srag\Plugins\Hub2\Origin\User\ARUserOrigin::updateDB();
\srag\Plugins\Hub2\Object\User\ARUser::updateDB();
\srag\Plugins\Hub2\Object\Course\ARCourse::updateDB();
\srag\Plugins\Hub2\Object\CourseMembership\ARCourseMembership::updateDB();
\srag\Plugins\Hub2\Object\Category\ARCategory::updateDB();
\srag\Plugins\Hub2\Object\Session\ARSession::updateDB();
\srag\Plugins\Hub2\Object\Group\ARGroup::updateDB();
\srag\Plugins\Hub2\Object\GroupMembership\ARGroupMembership::updateDB();
\srag\Plugins\Hub2\Object\SessionMembership\ARSessionMembership::updateDB();
\srag\Plugins\Hub2\Object\OrgUnit\AROrgUnit::updateDB();
\srag\Plugins\Hub2\Object\OrgUnitMembership\AROrgUnitMembership::updateDB();
?>
<#10>
<?php
try {
    \srag\Plugins\Hub2\Log\Log::updateDB();
} catch (\Throwable $ex) {
    // Fix Call to a member function getName() on null (Because not use ILIAS sequence)
}
?>
<#11>
<?php
\srag\Plugins\Hub2\Origin\CourseMembership\ARCourseMembershipOrigin::updateDB();
?>
<#12>
<?php
try {
    \srag\Plugins\Hub2\Log\Log::updateDB();
} catch (\Throwable $ex) {
    // Fix Call to a member function getName() on null (Because not use ILIAS sequence)
}
?>
<#13>
<?php
\srag\Plugins\Hub2\Origin\User\ARUserOrigin::updateDB();
?>
<#14>
<?php
$i = 1;
foreach ((new \srag\Plugins\Hub2\Origin\OriginFactory())->getAllActive() as $origin) {
	/**
	 * @var \srag\Plugins\Hub2\Origin\IOrigin $origin
	 */
	$origin->setSort($i);

	$origin->store();

	$i ++;
}
?>
<#15>
<?php
\srag\DIC\Hub2\DICStatic::dic()->database()->modifyTableColumn(\srag\Plugins\Hub2\Log\Log::TABLE_NAME, "object_ext_id", [
	"type" => "text",
	"length" => 255
]);
?>
<#16>
<?php
if (\srag\DIC\Hub2\DICStatic::dic()->database()->sequenceExists(\srag\Plugins\Hub2\Log\Log::TABLE_NAME)) {
	\srag\DIC\Hub2\DICStatic::dic()->database()->dropSequence(\srag\Plugins\Hub2\Log\Log::TABLE_NAME);
}

\srag\DIC\Hub2\DICStatic::dic()->database()->createAutoIncrement(\srag\Plugins\Hub2\Log\Log::TABLE_NAME, "log_id"); // Using MySQL native autoincrement for performance
?>
<#17>
<?php
\srag\Plugins\Hub2\Object\CompetenceManagement\ARCompetenceManagement::updateDB();
?>
<#18>
<?php
\srag\Plugins\Hub2\Log\Log::updateDB(); // new field 'status'
?>