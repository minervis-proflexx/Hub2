<?php

namespace srag\Plugins\Hub2\Origin\Properties\User;

use srag\Plugins\Hub2\Origin\Properties\IOriginProperties;

/**
 * Interface IUserProperties
 *
 * @package srag\Plugins\Hub2\Origin\Properties\User
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
interface IUserProperties extends IOriginProperties {

	const ACTIVATE_ACCOUNT = 'activate_account';
	const CREATE_PASSWORD = 'create_password';
	const SEND_PASSWORD = 'send_password';
	const RE_SEND_PASSWORD = 'resend_password';
	const SEND_PASSWORD_FIELD = 'send_password_field';
	const PASSWORD_MAIL_SUBJECT = 'password_mail_subject';
	const PASSWORD_MAIL_BODY = 'password_mail_body';
	const PASSWORD_MAIL_DATE_FORMAT = 'password_mail_date_format';
	const REACTIVATE_ACCOUNT = 'reactivate_account';
	const DELETE = 'delete';
	// How to handle the user if marked as TO_DELETE if data was not delivered
	// Default is "NONE" which means do nothing
	const DELETE_MODE_NONE = 0;
	const DELETE_MODE_DELETE = 1;
	const DELETE_MODE_INACTIVE = 2;


	/**
	 * @return array
	 */
	public static function getAvailableDeleteModes(): array;
}
