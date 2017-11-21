<?php

namespace SRAG\Plugins\Hub2\Object\Session;

use SRAG\Plugins\Hub2\Metadata\IMetadata;
use SRAG\Plugins\Hub2\Object\DTO\IMetadataAwareDataTransferObject;
use SRAG\Plugins\Hub2\Object\DTO\MetadataAwareDataTransferObject;

/**
 * Class SessionDTO
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class SessionDTO extends MetadataAwareDataTransferObject {

	const PARENT_ID_TYPE_REF_ID = 1;
	const PARENT_ID_TYPE_EXTERNAL_EXT_ID = 2;
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
	protected $title;
	/**
	 * @var string
	 */
	protected $description;
	/**
	 * @var string
	 */
	protected $location;
	/**
	 * @var string
	 */
	protected $details;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $phone;
	/**
	 * @var string
	 */
	protected $email;
	/**
	 * @var int
	 */
	protected $registrationType;
	/**
	 * @var bool
	 */
	protected $registrationLimited = false;
	/**
	 * @var int
	 */
	protected $registrationMinUsers;
	/**
	 * @var int
	 */
	protected $registrationMaxUsers;
	/**
	 * @var bool
	 */
	protected $registrationWaitingList;
	/**
	 * @var bool
	 */
	protected $waitingListAutoFill;
	/**
	 * @var bool
	 */
	protected $fullDay = false;
	/**
	 * @var int
	 */
	protected $start;
	/**
	 * @var int
	 */
	protected $end;


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 *
	 * @return SessionDTO
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
	 *
	 * @return SessionDTO
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getLocation() {
		return $this->location;
	}


	/**
	 * @param string $location
	 *
	 * @return SessionDTO
	 */
	public function setLocation($location) {
		$this->location = $location;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getDetails() {
		return $this->details;
	}


	/**
	 * @param string $details
	 *
	 * @return SessionDTO
	 */
	public function setDetails($details) {
		$this->details = $details;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 *
	 * @return SessionDTO
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getPhone() {
		return $this->phone;
	}


	/**
	 * @param string $phone
	 *
	 * @return SessionDTO
	 */
	public function setPhone($phone) {
		$this->phone = $phone;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}


	/**
	 * @param string $email
	 *
	 * @return SessionDTO
	 */
	public function setEmail($email) {
		$this->email = $email;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getRegistrationType() {
		return $this->registrationType;
	}


	/**
	 * @param int $registrationType
	 *
	 * @return SessionDTO
	 */
	public function setRegistrationType($registrationType) {
		$this->registrationType = $registrationType;

		return $this;
	}


	/**
	 * @return bool
	 */
	public function getRegistrationLimited() {
		return $this->registrationLimited;
	}


	/**
	 * @param bool $registrationLimited
	 *
	 * @return SessionDTO
	 */
	public function setRegistrationLimited($registrationLimited) {
		$this->registrationLimited = $registrationLimited;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getRegistrationMinUsers() {
		return $this->registrationMinUsers;
	}


	/**
	 * @param int $registrationMinUsers
	 *
	 * @return SessionDTO
	 */
	public function setRegistrationMinUsers($registrationMinUsers) {
		$this->registrationMinUsers = $registrationMinUsers;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getRegistrationMaxUsers() {
		return $this->registrationMaxUsers;
	}


	/**
	 * @param int $registrationMaxUsers
	 *
	 * @return SessionDTO
	 */
	public function setRegistrationMaxUsers($registrationMaxUsers) {
		$this->registrationMaxUsers = $registrationMaxUsers;

		return $this;
	}


	/**
	 * @return bool
	 */
	public function getRegistrationWaitingList() {
		return $this->registrationWaitingList;
	}


	/**
	 * @param bool $registrationWaitingList
	 *
	 * @return SessionDTO
	 */
	public function setRegistrationWaitingList($registrationWaitingList) {
		$this->registrationWaitingList = $registrationWaitingList;

		return $this;
	}


	/**
	 * @return bool
	 */
	public function getWaitingListAutoFill() {
		return $this->waitingListAutoFill;
	}


	/**
	 * @param bool $waitingListAutoFill
	 *
	 * @return SessionDTO
	 */
	public function setWaitingListAutoFill($waitingListAutoFill) {
		$this->waitingListAutoFill = $waitingListAutoFill;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getParentId() {
		return $this->parentId;
	}


	/**
	 * @param string $parentId
	 *
	 * @return SessionDTO
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
	 *
	 * @return SessionDTO
	 */
	public function setParentIdType($parentIdType) {
		$this->parentIdType = $parentIdType;

		return $this;
	}


	/**
	 * @return bool
	 */
	public function isFullDay() {
		return $this->fullDay;
	}


	/**
	 * @param bool $fullDay
	 *
	 * @return SessionDTO
	 */
	public function setFullDay($fullDay) {
		$this->fullDay = $fullDay;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getStart() {
		return $this->start;
	}


	/**
	 * @param int $start
	 *
	 * @return SessionDTO
	 */
	public function setStart($start) {
		$this->start = $start;

		return $this;
	}


	/**
	 * @return int
	 */
	public function getEnd() {
		return $this->end;
	}


	/**
	 * @param int $end Unix Timestamp
	 *
	 * @return SessionDTO
	 */
	public function setEnd($end) {
		$this->end = $end;

		return $this;
	}


	/**
	 * @return \SRAG\Plugins\Hub2\Object\Session\SessionDTO
	 */
	public function addMetadata(IMetadata $IMetadata): IMetadataAwareDataTransferObject {
		return parent::addMetadata($IMetadata); // TODO: Change the autogenerated stub
	}
}