<?php namespace SRAG\Hub2\Object;

/**
 * Class ARObject
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @package SRAG\ILIAS\Plugins\Hub2\Object
 */
abstract class ARObject extends \ActiveRecord implements IObject {

	/**
	 * @var array
	 */
	protected static $available_status = [
		IObject::STATUS_NEW,
		IObject::STATUS_TO_CREATE,
		IObject::STATUS_CREATED,
		IObject::STATUS_UPDATED,
		IObject::STATUS_TO_UPDATE,
		IObject::STATUS_TO_DELETE,
		IObject::STATUS_DELETED,
		IObject::STATUS_TO_UPDATE_NEWLY_DELIVERED,
		IObject::STATUS_IGNORED,
	];

	/**
	 * The primary ID is a composition of the origin-ID and ext_id
	 *
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_is_primary   true
	 * @db_fieldtype    text
	 * @db_length       255
	 */
	protected $id;

	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_is_notnull   true
	 * @db_length       8
	 * @db_index        true
	 */
	protected $origin_id;

	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       255
	 * @db_index        true
	 */
	protected $ext_id = '';

	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    timestamp
	 */
	protected $delivery_date;

	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    timestamp
	 */
	protected $processed_date;

	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       11
	 */
	protected $ilias_id;

	/**
	 * @var int
	 *
	 * @db_has_field    true
	 * @db_fieldtype    integer
	 * @db_length       11
	 * @db_index        true
	 */
	protected $status;

	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       255
	 */
	protected $period = '';

	/**
	 * @var string
	 *
	 * @db_has_field    true
	 * @db_fieldtype    text
	 * @db_length       512
	 */
	protected $hash_code;

	/**
	 * @var array
	 *
	 * @db_has_field    true
	 * @db_fieldtype    clob
	 */
	protected $data = array();

	public function update() {
		$this->hash_code = $this->getHashCode();
		parent::update();
	}

	public function create() {
		if (!$this->origin_id) {
			throw new \Exception("Origin-ID is missing, cannot construct the primary key");
		}
		if (!$this->ext_id) {
			throw new \Exception("External-ID is missing");
		}
		$this->id = $this->origin_id . $this->ext_id;
		$this->hash_code = $this->getHashCode();
		parent::create();
	}

	public function getId() {
		return $this->id;
	}

	public function getExtId() {
		return $this->ext_id;
	}

	public function getDeliveryDate() {
		return new \DateTime($this->delivery_date);
	}

	public function getProcessedDate() {
		return new \DateTime($this->processed_date);
	}

	public function setDeliveryDate($unix_timestamp) {
		$this->delivery_date = date('Y-m-d H:i:s', $unix_timestamp);
	}

	public function setProcessedDate($unix_timestamp) {
		$this->processed_date = date('Y-m-d H:i:s', $unix_timestamp);
	}

	public function getILIASId() {
		return $this->ilias_id;
	}

	public function setILIASId($id) {
		$this->ilias_id = (int) $id;
		return $this;
	}

	public function getStatus() {
		return $this->status;
	}

	public function setStatus($status) {
		if (!in_array($status, self::$available_status)) {
			throw new \InvalidArgumentException("'{$status}' is not a valid status");
		}
		$this->status = $status;
		return $this;
	}

	/**
	 * @param int $origin_id
	 * @return $this
	 */
	public function setOriginId($origin_id) {
		$this->origin_id = $origin_id;
		return $this;
	}

//	public function hasStatus($status) {
//		return (bool)$status & $this->status;
//	}
//
//	public function addStatus($status) {
//		$this->status = $this->status | $status;
//	}
//
//	public function removeStatus($status) {
//		$this->status = $this->status & ~$status;
//	}

	public function getPeriod() {
		return $this->period;
	}

	/**
	 * @inheritdoc
	 */
	public function computeHashCode() {
		$hash = '';
		foreach ($this->data as $property => $value) {
			$hash .= (is_array($value)) ? implode('', $value) : (string) $value;
		}
		return md5($hash);
	}

	/**
	 * @inheritdoc
	 */
	public function getHashCode() {
		return $this->hash_code;
	}

	/**
	 * @inheritdoc
	 */
	public function setData(array $data) {
//		foreach ($data as $key => $value) {
//			$this->{$key} = $value;
//		}
		$this->data = $data;
		if (isset($data['period'])) {
			$this->period = $data['period'];
		}
	}

	/**
	 * @return string
	 */
	function __toString() {
		return implode(', ', [
			"origin_id: " . $this->origin_id,
			"type: " . get_class($this),
			"ext_id: " . $this->getExtId(),
			"ilias_id: " . $this->getILIASId(),
			"status: " . $this->getStatus(),
		]);
	}


}