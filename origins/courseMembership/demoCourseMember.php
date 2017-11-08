<?php namespace SRAG\Hub2\Origin;

use SRAG\Hub2\Exception\BuildObjectsFailedException;
use SRAG\Hub2\Exception\ConnectionFailedException;
use SRAG\Hub2\Exception\ParseDataFailedException;
use SRAG\Hub2\Object\Course\CourseDTO;
use SRAG\Hub2\Object\CourseMembership\CourseMembershipDTO;
use SRAG\Hub2\Object\HookObject;
use SRAG\Hub2\Object\IDataTransferObject;
use SRAG\Hub2\Origin\Config\OriginImplementationFactory;

/**
 * Class demoCourseMember
 *
 * @package SRAG\Hub2\Origin
 */
class demoCourseMember extends AbstractOriginImplementation {

	const EXT_COURSE_ID = 71;
	const EXT_USER_ID = 6;


	/**
	 * Connect to the service providing the sync data.
	 * Throw a ConnectionFailedException to abort the sync if a connection is not possible.
	 *
	 * @throws ConnectionFailedException
	 * @return bool
	 */
	public function connect() { return true; }


	/**
	 * Parse and prepare (sanitize/validate) the data to fill the DTO objects.
	 * Return the number of data. Note that this number is used to check if the amount of delivered
	 * data is sufficent to continue the sync, depending on the configuration of the origin.
	 *
	 * Throw a ParseDataFailedException to abort the sync if your data cannot be parsed.
	 *
	 * @throws ParseDataFailedException
	 * @return int
	 */
	public function parseData() {
		//		global $DIC;
		//		$f = new OriginFactory($DIC->database());
		//		/**
		//		 * @var $demoCourse \SRAG\Hub2\Origin\demoCourse
		//		 */
		//		$demoCourse = $f->getById(3);
		//		$demoCourse->parseData();
		//		foreach ($demoCourse->buildObjects() as $CourseDTO) {
		//
		//		}
		//		/**
		//		 * @var $demoUser \SRAG\Hub2\Origin\demoUser
		//		 * @var $UserDTO  \SRAG\Hub2\Object\User\UserDTO
		//		 */
		//		$demoUser = $f->getById(4);
		//
		//		foreach ($demoUser->buildObjects() as $UserDTO) {
		//
		//		}

		$this->data[] = $this->factory()
		                     ->courseMembership(self::EXT_COURSE_ID, self::EXT_USER_ID)
		                     ->setIliasCourseRefId(self::EXT_COURSE_ID)
		                     ->setRole(CourseMembershipDTO::ROLE_TUTOR)
		                     ->setUserId(self::EXT_USER_ID);
		//		$this->data[] = $this->factory()
		//		                     ->courseMembership(69, 6)
		//		                     ->setIliasCourseRefId(69)
		//		                     ->setRole(CourseMembershipDTO::ROLE_TUTOR)
		//		                     ->setUserId(10458);

		return count($this->data);
	}


	/**
	 * Build the hub DTO objects from the parsed data.
	 * An instance of such objects MUST be obtained over the DTOObjectFactory. The factory
	 * is available via $this->factory().
	 *
	 * Example for an origin syncing users:
	 *
	 * $user = $this->factory()->user($data->extId) {   }
	 * $user->setFirstname($data->firstname)
	 *  ->setLastname($data->lastname)
	 *  ->setGender(UserDTO::GENDER_FEMALE) {   }
	 *
	 * Throw a BuildObjectsFailedException to abort the sync at this stage.
	 *
	 * @throws BuildObjectsFailedException
	 * @return IDataTransferObject[]
	 */
	public function buildObjects() {
		return $this->data;
	}


	// HOOKS
	// ------------------------------------------------------------------------------------------------------------

	/**
	 * Called if any exception occurs during processing the ILIAS objects. This hook can be used to
	 * influence the further processing of the current origin sync or the global sync:
	 *
	 * - Throw an AbortOriginSyncException to stop the current sync of this origin.
	 *   Any other following origins in the processing chain are still getting executed normally.
	 * - Throw an AbortOriginSyncOfCurrentTypeException to abort the current sync of the origin AND
	 *   all also skip following syncs from origins of the same object type, e.g. User, Course etc.
	 * - Throw an AbortSyncException to stop the global sync. The sync of any other following
	 * origins in the processing chain is NOT getting executed.
	 *
	 * Note that if you do not throw any of the exceptions above, the sync will continue.
	 *
	 * @param \Exception $e
	 */
	public function handleException(\Exception $e) { }


	/**
	 * @param HookObject $object
	 */
	public function beforeCreateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function afterCreateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function beforeUpdateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function afterUpdateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function beforeDeleteILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function afterDeleteILIASObject(HookObject $object) { }


	/**
	 * Executed before the synchronization of the origin is executed.
	 */
	public function beforeSync() { }


	/**
	 * Executed after the synchronization of the origin has been executed.
	 */
	public function afterSync() { }
}