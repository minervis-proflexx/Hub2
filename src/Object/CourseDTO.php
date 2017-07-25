<?php namespace SRAG\Hub2\Object;

/**
 * Class CourseDTO
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @package SRAG\Hub2\Object
 */
class CourseDTO extends DataTransferObject {

	// @see ilCourseConstants
	const SUBSCRIPTION_TYPE_DEACTIVATED = 1;
	const SUBSCRIPTION_TYPE_REQUEST_MEMBERSHIP = 2;
	const SUBSCRIPTION_TYPE_DIRECTLY = 3;
	const SUBSCRIPTION_TYPE_PASSWORD = 4;

	const VIEW_MODE_SESSIONS = 0;
	const VIEW_MODE_OBJECTIVES = 1;
	const VIEW_MODE_TIMING = 2;
	const VIEW_MODE_SIMPLE = 4;
	const VIEW_MODE_BY_TYPE = 5;

	const PARENT_ID_TYPE_REF_ID = 1;
	const PARENT_ID_TYPE_EXTERNAL_EXT_ID = 2;

	/**
	 * @var array
	 */
	protected static $subscriptionTypes = [
		self::SUBSCRIPTION_TYPE_DEACTIVATED,
		self::SUBSCRIPTION_TYPE_REQUEST_MEMBERSHIP,
		self::SUBSCRIPTION_TYPE_DIRECTLY,
		self::SUBSCRIPTION_TYPE_PASSWORD,
	];

	/**
	 * @var array
	 */
	protected static $viewModes = [
		self::VIEW_MODE_SESSIONS,
		self::VIEW_MODE_OBJECTIVES,
		self::VIEW_MODE_TIMING,
		self::VIEW_MODE_SIMPLE,
		self::VIEW_MODE_BY_TYPE
	];

	/**
	 * @var array
	 */
	protected static $parentIdTypes = [
		self::PARENT_ID_TYPE_REF_ID,
		self::PARENT_ID_TYPE_EXTERNAL_EXT_ID,
	];

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $importantInformation;

	/**
	 * @var string
	 */
	protected $contactResponsibility;

	/**
	 * @var string
	 */
	protected $contactEmail;

	/**
	 * @var string
	 */
	protected $parentId;

	/**
	 * @var int
	 */
	protected $parentIdType = self::PARENT_ID_TYPE_REF_ID;

	/**
	 * @var string
	 */
	protected $firstDependenceCategory;

	/**
	 * @var string
	 */
	protected $secondDependenceCategory;

	/**
	 * @var string
	 */
	protected $thirdDependenceCategory;

	/**
	 * @var array
	 */
	protected $notificationEmails = [];

	/**
	 * @var int
	 */
	protected $owner = 6;

	/**
	 * @var int
	 */
	protected $subscriptionLimitationType = 0;

	/**
	 * @var int
	 */
	protected $viewMode = self::VIEW_MODE_SESSIONS;


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return CourseDTO
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return CourseDTO
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getImportantInformation() {
		return $this->importantInformation;
	}

	/**
	 * @param string $importantInformation
	 * @return CourseDTO
	 */
	public function setImportantInformation($importantInformation) {
		$this->importantInformation = $importantInformation;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getContactResponsibility() {
		return $this->contactResponsibility;
	}

	/**
	 * @param string $contactResponsibility
	 * @return CourseDTO
	 */
	public function setContactResponsibility($contactResponsibility) {
		$this->contactResponsibility = $contactResponsibility;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getContactEmail() {
		return $this->contactEmail;
	}

	/**
	 * @param string $contactEmail
	 * @return CourseDTO
	 */
	public function setContactEmail($contactEmail) {
		$this->contactEmail = $contactEmail;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstDependenceCategory() {
		return $this->firstDependenceCategory;
	}

	/**
	 * @param string $firstDependenceCategory
	 * @return CourseDTO
	 */
	public function setFirstDependenceCategory($firstDependenceCategory) {
		$this->firstDependenceCategory = $firstDependenceCategory;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSecondDependenceCategory() {
		return $this->secondDependenceCategory;
	}

	/**
	 * @param string $secondDependenceCategory
	 * @return CourseDTO
	 */
	public function setSecondDependenceCategory($secondDependenceCategory) {
		$this->secondDependenceCategory = $secondDependenceCategory;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getThirdDependenceCategory() {
		return $this->thirdDependenceCategory;
	}

	/**
	 * @param string $thirdDependenceCategory
	 * @return CourseDTO
	 */
	public function setThirdDependenceCategory($thirdDependenceCategory) {
		$this->thirdDependenceCategory = $thirdDependenceCategory;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getNotificationEmails() {
		return $this->notificationEmails;
	}

	/**
	 * @param array $notificationEmails
	 * @return CourseDTO
	 */
	public function setNotificationEmails($notificationEmails) {
		$this->notificationEmails = $notificationEmails;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param int $owner
	 * @return CourseDTO
	 */
	public function setOwner($owner) {
		$this->owner = (int) $owner;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSubscriptionLimitationType() {
		return $this->subscriptionLimitationType;
	}

	/**
	 * @param int $subscriptionLimitationType
	 * @return CourseDTO
	 */
	public function setSubscriptionLimitationType($subscriptionLimitationType) {
		if (!in_array($subscriptionLimitationType, self::$subscriptionTypes)) {
			throw new \InvalidArgumentException("Given $subscriptionLimitationType does not exist");
		}
		$this->subscriptionLimitationType = $subscriptionLimitationType;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getViewMode() {
		return $this->viewMode;
	}

	/**
	 * @param int $viewMode
	 * @return CourseDTO
	 */
	public function setViewMode($viewMode) {
		if (!in_array($viewMode, self::$viewModes)) {
			throw new \InvalidArgumentException("Given $viewMode does not exist");
		}
		$this->viewMode = $viewMode;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getParentId() {
		return $this->parentId;
	}

	/**
	 * @param int $parentId
	 * @return $this
	 */
	public function setParentId($parentId) {
		$this->parentId = $parentId;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getParentIdType() {
		return $this->parentIdType;
	}

	/**
	 * @param int $parentIdType
	 * @return CourseDTO
	 */
	public function setParentIdType($parentIdType) {
		if (!in_array($parentIdType, self::$parentIdTypes)) {
			throw new \InvalidArgumentException("Invalid parentIdType given '$parentIdType'");
		}
		$this->parentIdType = $parentIdType;
		return $this;
	}

}