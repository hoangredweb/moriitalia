<?php
/**
 * IP2Location record file.
 *
 * @package    IP2Location.Library
 * @copyright  Copyright (C) 2005-2015 redCOMPONENT.com/IP2Location.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 *
 * This library is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die;

/**
 * Class IP2LocationRecord
 *
 * @package     IP2Location.Library
 * @subpackage  Class.IP2LocationRecord
 * @since       1.0.0
 *
 */
class IP2LocationRecord
{
	/**
	 * Location ip address.
	 *
	 * @var string
	 */
	public $ipAddress;

	/**
	 * Location ip number.
	 *
	 * @var int
	 */
	public $ipNumber;

	/**
	 * Country code.
	 *
	 * @var string
	 */
	public $countryCode;

	/**
	 * Country name.
	 *
	 * @var string
	 */
	public $countryName;

	/**
	 * Region name.
	 *
	 * @var string
	 */
	public $regionName;

	/**
	 * City name.
	 *
	 * @var string
	 */
	public $cityName;

	/**
	 * Location latitude.
	 *
	 * @var float
	 */
	public $latitude;

	/**
	 * Location longitude.
	 *
	 * @var float
	 */
	public $longitude;

	/**
	 * Location internet service provider id.
	 *
	 * @var string
	 */
	public $isp;

	/**
	 * Domain name.
	 *
	 * @var string
	 */
	public $domainName;

	/**
	 * City zip code.
	 *
	 * @var string
	 */
	public $zipCode;

	/**
	 * Location timezone.
	 *
	 * @var string
	 */
	public $timeZone;

	/**
	 * Location average internet speed.
	 *
	 * @var string
	 */
	public $netSpeed;

	/**
	 * Idd code.
	 *
	 * @var string
	 */
	public $iddCode;

	/**
	 * Location area code.
	 *
	 * @var string
	 */
	public $areaCode;

	/**
	 * Weather station code.
	 *
	 * @var string
	 */
	public $weatherStationCode;

	/**
	 * Weather station name.
	 *
	 * @var string
	 */
	public $weatherStationName;

	/**
	 * Mcc code.
	 *
	 * @var string
	 */
	public $mcc;

	/**
	 * Mnc code.
	 *
	 * @var string
	 */
	public $mnc;

	/**
	 * Mobile carrier name.
	 *
	 * @var string
	 */
	public $mobileCarrierName;

	/**
	 * Elevation value.
	 *
	 * @var string
	 */
	public $elevation;

	/**
	 * Usage type.
	 *
	 * @var string
	 */
	public $usageType;
}
