<?php
/**
 * IP2Location library file.
 * Including this file into your application will make ip2location available to use.
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
 * Class IP2Location.
 *
 * @package     IP2Location.Library
 * @subpackage  Class.IP2Location
 * @since       1.0.0
 *
 */
class IP2Location
{
	// Current version.
	const VERSION = '7.0.0';

	// Database storage method.
	const FILE_IO = 0;
	const MEMORY_CACHE = 1;
	const SHARED_MEMORY = 2;

	// Unpack method.
	const ENDIAN = 0;
	const BIG_ENDIAN = 1;

	// Record field.
	const ALL = 100;
	const COUNTRY_CODE = 1;
	const COUNTRY_NAME = 2;
	const REGION_NAME = 3;
	const CITY_NAME = 4;
	const LATITUDE = 5;
	const LONGITUDE = 6;
	const ISP = 7;
	const DOMAIN_NAME = 8;
	const ZIP_CODE = 9;
	const TIME_ZONE = 10;
	const NET_SPEED = 11;
	const IDD_CODE = 12;
	const AREA_CODE = 13;
	const WEATHER_STATION_CODE = 14;
	const WEATHER_STATION_NAME = 15;
	const MCC = 16;
	const MNC = 17;
	const MOBILE_CARRIER_NAME = 18;
	const ELEVATION = 19;
	const USAGE_TYPE = 20;

	// IP version.
	const IPV4 = 0;
	const IPV6 = 1;

	// SHMOP memory address.
	const SHM_KEY = 4194500608;

	// Message.
	const FIELD_NOT_SUPPORTED = false;

	/**
	 * Array of columns indexes per database.
	 * There are 25 databases.
	 *
	 * @var array
	 */
	private $columns = array(
		'COUNTRY_CODE' => array(
			0, 2, 2, 2, 2,
			2, 2, 2, 2, 2,
			2, 2, 2, 2, 2,
			2, 2, 2, 2, 2,
			2, 2, 2, 2, 2
		),
		'COUNTRY_NAME' => array(
			0, 2, 2, 2, 2,
			2, 2, 2, 2, 2,
			2, 2, 2, 2, 2,
			2, 2, 2, 2, 2,
			2, 2, 2, 2, 2
		),
		'REGION_NAME'  => array(
			0, 0, 0, 3, 3,
			3, 3, 3, 3, 3,
			3, 3, 3, 3, 3,
			3, 3, 3, 3, 3,
			3, 3, 3, 3, 3
		),
		'CITY_NAME'    => array(
			0, 0, 0, 4, 4,
			4, 4, 4, 4, 4,
			4, 4, 4, 4, 4,
			4, 4, 4, 4, 4,
			4, 4, 4, 4, 4
		),
		'LATITUDE'     => array(
			0, 0, 0, 0, 0,
			5, 5, 0, 5, 5,
			5, 5, 5, 5, 5,
			5, 5, 5, 5, 5,
			5, 5, 5, 5, 5
		),
		'LONGITUDE'    => array(
			0, 0, 0, 0, 0,
			6, 6, 0, 6, 6,
			6, 6, 6, 6, 6,
			6, 6, 6, 6, 6,
			6, 6, 6, 6, 6
		),
		'ISP'          => array(
			0, 0, 3, 0, 5,
			0, 7, 5, 7, 0,
			8, 0, 9, 0, 9,
			0, 9, 0, 9, 7,
			9, 0, 9, 7, 9
		),
		'DOMAIN_NAME'  => array(
			0, 0, 0, 0, 0,
			0, 0, 6, 8, 0,
			9, 0, 10, 0, 10,
			0, 10, 0, 10, 8,
			10, 0, 10, 8, 10
		),
		'ZIP_CODE'     => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 7,
			7, 7, 7, 0, 7,
			7, 7, 0, 7, 0,
			7, 7, 7, 0, 7
		),
		'TIME_ZONE'    => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 8, 8, 7, 8,
			8, 8, 7, 8, 0,
			8, 8, 8, 0, 8
		),
		'NET_SPEED'    => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 8, 11,
			0, 11, 8, 11, 0,
			11, 0, 11, 0, 11,
		),
		'IDD_CODE'     => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			9, 12, 0, 12, 0,
			12, 9, 12, 0, 12
		),
		'AREA_CODE'    => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			10, 13, 0, 13, 0,
			13, 10, 13, 0, 13
		),
		'ELEVATION'    => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 11, 19, 0, 19
		),
		'USAGE_TYPE'   => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 12, 20
		),
		'MCC'          => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 9,
			16, 0, 16, 9, 16
		),
		'MNC'          => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 10,
			17, 0, 17, 10, 17
		),
		'WEATHER_STATION_CODE' => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 9, 14, 0,
			14, 0, 14, 0, 14
		),
		'WEATHER_STATION_NAME' => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 10, 15, 0,
			15, 0, 15, 0, 15
		),
		'MOBILE_CARRIER_NAME'  => array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 11,
			18, 0, 18, 11, 18
		),
	);

	private $shmId = '';

	private $database = array();

	private $unpackMethod;

	private $buffer;

	private $mode;

	private $resource;

	private $result;

	/**
	 * Class constructor.
	 *
	 * @param   string  $file  File location to open. Can open .bin or .csv files.
	 * @param   int     $mode  IO mode.
	 *
	 * @throws Exception
	 */
	public function __construct($file = '', $mode = self::FILE_IO)
	{
		if (!file_exists($file))
		{
			throw new Exception(JText::sprintf('LIB_IP2LOCATION_CANT_LOCATE_FILE', $file));
		}

		// Define system unpack method.
		list($test) = array_values(unpack('L1L', pack('V', 1)));

		// Use Big Endian Unpack if endian test failed.
		$this->unpackMethod = (($test != 1)) ? self::BIG_ENDIAN : self::ENDIAN;

		switch ($mode)
		{
			case self::SHARED_MEMORY:
				if (!function_exists('shmop_open'))
				{
					throw new Exception(JText::_('LIB_IP2LOCATION_SHMOP_ERROR'));
				}

				$this->mode = self::SHARED_MEMORY;

				$this->shmId = @shmop_open(self::SHM_KEY, 'a', 0, 0);

				if ($this->shmId === false)
				{
					// First execution, load database into memory.
					if (($fp = fopen($file, 'rb')) === false)
					{
						throw new Exception(JText::sprintf('LIB_IP2LOCATION_CANT_OPEN_FILE', $file));
					}

					$stats = fstat($fp);

					if ($shm_id = @shmop_open(self::SHM_KEY, 'w', 0, 0))
					{
						shmop_delete($shm_id);
						shmop_close($shm_id);
					}

					if ($shm_id = @shmop_open(self::SHM_KEY, 'c', 0644, $stats['size']))
					{
						$pointer = 0;

						while ($pointer < $stats['size'])
						{
							$buf = fread($fp, 524288);
							shmop_write($shm_id, $buf, $pointer);
							$pointer += 524288;
						}

						shmop_close($shm_id);
					}

					fclose($fp);

					$this->shmId = @shmop_open(self::SHM_KEY, 'a', 0, 0);

					if ($this->shmId === false)
					{
						throw new Exception(JText::_('LIB_IP2LOCATION_CANT_ACCESS_SHARED_MEMORY_BLOCK'));
					}
				}

				break;

			default:
				$this->mode     = self::FILE_IO;
				$this->resource = fopen($file, 'rb');

				if ($mode == self::MEMORY_CACHE)
				{
					$this->mode   = self::MEMORY_CACHE;
					$stats        = fstat($this->resource);
					$this->buffer = fread($this->resource, $stats['size']);
				}
		}

		$this->database['type']              = $this->readByte(1, '8');
		$this->database['column']            = $this->readByte(2, '8');
		$this->database['year']              = $this->readByte(3, '8');
		$this->database['month']             = $this->readByte(4, '8');
		$this->database['day']               = $this->readByte(5, '8');
		$this->database['ipv4_count']        = $this->readByte(6, '32');
		$this->database['ipv4_base_address'] = $this->readByte(10, '32');
		$this->database['ipv6_count']        = $this->readByte(14, '32');
		$this->database['ipv6_base_address'] = $this->readByte(18, '32');

		$this->result = new IP2LocationRecord;
	}

	/**
	 * Read byte function.
	 *
	 * @param   int     $pos        Position to read.
	 * @param   string  $mode       Read mode.
	 * @param   bool    $auto_size  Auto size output.
	 *
	 * @return  int|string  Read byte.
	 */
	private function readByte($pos, $mode = 'string', $auto_size = false)
	{
		switch ($this->mode)
		{
			case self::SHARED_MEMORY:
				if ($mode == 'string')
				{
					$data = shmop_read($this->shmId, $pos, ($auto_size) ? shmop_size($this->shmId) - $pos : 100);
				}
				else
				{
					$data = shmop_read($this->shmId, $pos - 1, 50);
				}

				break;

			case self::MEMORY_CACHE:
				$data = substr($this->buffer, (($mode == 'string') ? $pos : $pos - 1), 100);

				break;

			default:
				if ($mode == 'string')
				{
					fseek($this->resource, $pos, SEEK_SET);
					$data = @fread($this->resource, 1);
				}
				else
				{
					fseek($this->resource, $pos - 1, SEEK_SET);
					$data = @fread($this->resource, 50);
				}
		}

		switch ($mode)
		{
			case '8':
				$out    = $this->readBinary('C', $data);
				$result = $out[1];

				break;

			case '32':
				$out = $this->readBinary('V', $data);

				if ($out[1] < 0)
				{
					$out[1] += 4294967296;
				}

				$result = (int) $out[1];

				break;

			case '128':
				$array    = preg_split('//', $data, -1, PREG_SPLIT_NO_EMPTY);
				$ip96_127 = $this->readBinary('V', $array[0] . $array[1] . $array[2] . $array[3]);
				$ip64_95  = $this->readBinary('V', $array[4] . $array[5] . $array[6] . $array[7]);
				$ip32_63  = $this->readBinary('V', $array[8] . $array[9] . $array[10] . $array[11]);
				$ip1_31   = $this->readBinary('V', $array[12] . $array[13] . $array[14] . $array[15]);

				if ($ip96_127[1] < 0)
				{
					$ip96_127[1] += 4294967296;
				}

				if ($ip64_95[1] < 0)
				{
					$ip64_95[1] += 4294967296;
				}

				if ($ip32_63[1] < 0)
				{
					$ip32_63[1] += 4294967296;
				}

				if ($ip1_31[1] < 0)
				{
					$ip1_31[1] += 4294967296;
				}

				$result = bcadd(
					bcadd(
						bcmul($ip1_31[1], bcpow(4294967296, 3)),
						bcmul($ip32_63[1], bcpow(4294967296, 2))
					),
					bcadd(
						bcmul($ip64_95[1], 4294967296),
						$ip96_127[1]
					)
				);

				break;

			case 'float':
				$out = $this->readBinary('f', $data);

				$result = $out[1];

				break;

			default:
				$out = $this->readBinary('C', $data);

				if (in_array($this->mode, array(self::SHARED_MEMORY, self::MEMORY_CACHE)))
				{
					$result = substr($data, 1, $out[1]);
				}
				else
				{
					$result = @fread($this->resource, $out[1]);
				}
		}

		return $result;
	}

	/**
	 * Read data binary.
	 *
	 * @param   string  $format  Read format.
	 * @param   string  $data    Data to read.
	 *
	 * @return  array  Read binary data.
	 */
	private function readBinary($format, $data)
	{
		if ($this->unpackMethod == self::BIG_ENDIAN)
		{
			$ar   = unpack($format, $data);
			$vals = array_values($ar);
			$f    = explode('/', $format);
			$i    = 0;

			foreach ($f as $f_value)
			{
				$repeater = intval(substr($f_value, 1));

				if ($repeater == 0)
				{
					$repeater = 1;
				}

				if ($f_value{1} == '*')
				{
					$repeater = count($ar) - $i;
				}

				if ($f_value{0} != 'd')
				{
					$i += $repeater;

					continue;
				}

				$j = $i + $repeater;

				for ($a = $i; $a < $j; ++$a)
				{
					$p = pack('d', $vals[$i]);
					$p = strrev($p);
					list($vals[$i]) = array_values(unpack('d1d', $p));
					++$i;
				}
			}

			$a = 0;

			foreach ($ar as $ar_key => $ar_value)
			{
				$ar[$ar_key] = $vals[$a];
				++$a;
			}

			return $ar;
		}

		return unpack($format, $data);
	}

	/**
	 * Convert IPv6 into long integer (string-database format).
	 *
	 * @param   string  $ipv6  IPv6 address.
	 *
	 * @return  string  IPv6 long integer value.
	 */
	private function ipv6Numeric($ipv6)
	{
		$ip_n = inet_pton($ipv6);
		$bits = 15;

		// 16 x 8 bit = 128bit
		$ipv6long = 0;

		while ($bits >= 0)
		{
			$bin = sprintf("%08b", (ord($ip_n[$bits])));

			if ($ipv6long)
			{
				$ipv6long = $bin . $ipv6long;
			}
			else
			{
				$ipv6long = $bin;
			}

			$bits--;
		}

		return gmp_strval(gmp_init($ipv6long, 2), 10);
	}

	/**
	 * Validate ip address.
	 *
	 * @param   string  $ip  IP address for validation.
	 *
	 * @return  bool|int  Validate code on success (4-IPv4, 6-IPv6), false otherwise.
	 */
	private function validate($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			return 4;
		}

		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		{
			return 6;
		}

		return false;
	}

	/**
	 * Core function to lookup geolocation data.
	 *
	 * @param   string  $ip      Ip for lookup. v4 or v6
	 * @param   int     $fields  Fields to get from database.
	 *
	 * @return  int|IP2LocationRecord|string
	 */
	public function lookup($ip, $fields = self::ALL)
	{
		$this->result->ipAddress = $ip;

		if (($version = $this->validate($ip)) === false)
		{
			foreach ($this->result as &$obj)
			{
				if ($obj)
				{
					continue;
				}

				$obj = null;
			}

			return $this->result;
		}

		if ($version == 4)
		{
			return $this->ipv4Lookup($ip, $fields);
		}

		if ($version == 6)
		{
			return $this->ipv6Lookup($ip, $fields);
		}
	}

	/**
	 * Lookup for IPv4 records.
	 *
	 * @param   string  $ip      Ip address.
	 * @param   int     $fields  Number of fields to read.
	 *
	 * @return int|IP2LocationRecord|string
	 */
	public function ipv4Lookup($ip, $fields)
	{
		$keys                   = array_keys($this->columns);
		$base_address           = $this->database['ipv4_base_address'];
		$high                   = $this->database['ipv4_count'];
		$ip_number              = sprintf('%u', ip2long($ip));
		$ip_number              = ($ip_number >= 4294967295) ? ($ip_number - 1) : $ip_number;
		$this->result->ipNumber = $ip_number;
		$low                    = 0;

		while ($low <= $high)
		{
			$mid     = (int) (($low + $high) / 2);
			$ip_from = $this->readByte($base_address + $mid * ($this->database['column'] * 4), 32);
			$ip_to   = $this->readByte($base_address + ($mid + 1) * ($this->database['column'] * 4), 32);

			if ($ip_from < 0)
			{
				$ip_from += pow(2, 32);
			}

			if ($ip_to < 0)
			{
				$ip_to += pow(2, 32);
			}

			if (($ip_number >= $ip_from) && ($ip_number < $ip_to))
			{
				$pointer = $base_address + ($mid * $this->database['column'] * 4);

				switch ($fields)
				{
					case self::COUNTRY_CODE:
					case self::REGION_NAME:
					case self::CITY_NAME:
					case self::ISP:
					case self::DOMAIN_NAME:
					case self::ZIP_CODE:
					case self::TIME_ZONE:
					case self::NET_SPEED:
					case self::IDD_CODE:
					case self::AREA_CODE:
					case self::WEATHER_STATION_CODE:
					case self::WEATHER_STATION_NAME:
					case self::MCC:
					case self::MNC:
					case self::MOBILE_CARRIER_NAME:
					case self::ELEVATION:
						$return = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), '32'),
							'string',
							true
						);

						break;

					case self::COUNTRY_NAME:
						$return = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), '32') + 3,
							'string',
							true
						);

						break;

					case self::LATITUDE:
					case self::LONGITUDE:
						$return = $this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), 'float', true);

						break;

					case self::USAGE_TYPE:
						$return = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), '32'),
							'string',
							true
						);

						break;

					default:
						// Default setter
						$this->result->regionName         = self::FIELD_NOT_SUPPORTED;
						$this->result->cityName           = self::FIELD_NOT_SUPPORTED;
						$this->result->latitude           = self::FIELD_NOT_SUPPORTED;
						$this->result->longitude          = self::FIELD_NOT_SUPPORTED;
						$this->result->isp                = self::FIELD_NOT_SUPPORTED;
						$this->result->domainName         = self::FIELD_NOT_SUPPORTED;
						$this->result->zipCode            = self::FIELD_NOT_SUPPORTED;
						$this->result->timeZone           = self::FIELD_NOT_SUPPORTED;
						$this->result->netSpeed           = self::FIELD_NOT_SUPPORTED;
						$this->result->iddCode            = self::FIELD_NOT_SUPPORTED;
						$this->result->areaCode           = self::FIELD_NOT_SUPPORTED;
						$this->result->weatherStationCode = self::FIELD_NOT_SUPPORTED;
						$this->result->weatherStationName = self::FIELD_NOT_SUPPORTED;
						$this->result->mcc                = self::FIELD_NOT_SUPPORTED;
						$this->result->mnc                = self::FIELD_NOT_SUPPORTED;
						$this->result->mobileCarrierName  = self::FIELD_NOT_SUPPORTED;
						$this->result->elevation          = self::FIELD_NOT_SUPPORTED;
						$this->result->usageType          = self::FIELD_NOT_SUPPORTED;

						// Reading data from file
						$this->result->countryCode = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']] - 1), '32'),
							'string',
							true
						);
						$this->result->countryName = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_NAME - 1]][$this->database['type']] - 1), '32') + 3,
							'string',
							true
						);

						if ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->regionName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->cityName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']] != 0)
						{
							$this->result->latitude = $this->readByte(
								$pointer + 4 * ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']] - 1),
								'float',
								true
							);
						}

						if ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']] != 0)
						{
							$this->result->longitude = $this->readByte(
								$pointer + 4 * ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']] - 1),
								'float',
								true
							);
						}

						if ($this->columns[$keys[self::ISP - 1]][$this->database['type']] != 0)
						{
							$this->result->isp = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::ISP - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->domainName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->zipCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']] != 0)
						{
							$this->result->timeZone = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']] != 0)
						{
							$this->result->netSpeed = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->iddCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->areaCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->weatherStationCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->weatherStationName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::MCC - 1]][$this->database['type']] != 0)
						{
							$this->result->mcc = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::MCC - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::MNC - 1]][$this->database['type']] != 0)
						{
							$this->result->mnc = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::MNC - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->mobileCarrierName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']] != 0)
						{
							$this->result->elevation = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']] != 0)
						{
							$this->result->usageType = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']] - 1), '32'),
								'string',
								true
							);
						}

						return $this->result;
				}

				return $return;
			}
			else
			{
				if ($ip_number < $ip_from)
				{
					$high = $mid - 1;
				}
				else
				{
					$low = $mid + 1;
				}
			}
		}
	}

	/**
	 * Lookup for IPv6 records.
	 *
	 * @param   string  $ip      Ip address.
	 * @param   int     $fields  Number of fields to read.
	 *
	 * @return int|IP2LocationRecord|string
	 */
	public function ipv6Lookup($ip, $fields)
	{
		$keys                   = array_keys($this->columns);
		$base_address           = $this->database['ipv6_base_address'];
		$ip_number              = $this->ipv6Numeric($ip);
		$this->result->ipNumber = $ip_number;
		$low                    = 0;
		$high                   = $this->database['ipv6_count'];

		while ($low <= $high)
		{
			$mid     = (int) (($low + $high) / 2);
			$ip_from = $this->readByte($base_address + $mid * ($this->database['column'] * 4 + 12), 128);
			$ip_to   = $this->readByte($base_address + ($mid + 1) * ($this->database['column'] * 4 + 12), 128);

			if ($ip_from < 0)
			{
				$ip_from += pow(2, 32);
			}

			if ($ip_to < 0)
			{
				$ip_to += pow(2, 32);
			}

			if (($ip_number >= $ip_from) && ($ip_number < $ip_to))
			{
				$pointer = $base_address + ($mid * ($this->database['column'] * 4 + 12)) + 8;

				switch ($fields)
				{
					case self::COUNTRY_CODE:
					case self::REGION_NAME:
					case self::CITY_NAME:
					case self::ISP:
					case self::DOMAIN_NAME:
					case self::ZIP_CODE:
					case self::TIME_ZONE:
					case self::NET_SPEED:
					case self::IDD_CODE:
					case self::AREA_CODE:
					case self::WEATHER_STATION_CODE:
					case self::WEATHER_STATION_NAME:
					case self::MCC:
					case self::MNC:
					case self::MOBILE_CARRIER_NAME:
					case self::ELEVATION:
						$return = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), '32'),
							'string',
							true
						);

						break;

					case self::COUNTRY_NAME:
						$return = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), '32') + 3,
							'string',
							true
						);

						break;

					case self::LATITUDE:
					case self::LONGITUDE:
						$return = $this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), 'float', true);

						break;

					case self::USAGE_TYPE:
						$return = $this->readByte(
							$this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), '32'),
							'string',
							true
						);

						break;

					default:
						// Default setter
						$this->result->regionName         = self::FIELD_NOT_SUPPORTED;
						$this->result->cityName           = self::FIELD_NOT_SUPPORTED;
						$this->result->latitude           = self::FIELD_NOT_SUPPORTED;
						$this->result->longitude          = self::FIELD_NOT_SUPPORTED;
						$this->result->isp                = self::FIELD_NOT_SUPPORTED;
						$this->result->domainName         = self::FIELD_NOT_SUPPORTED;
						$this->result->zipCode            = self::FIELD_NOT_SUPPORTED;
						$this->result->timeZone           = self::FIELD_NOT_SUPPORTED;
						$this->result->netSpeed           = self::FIELD_NOT_SUPPORTED;
						$this->result->iddCode            = self::FIELD_NOT_SUPPORTED;
						$this->result->areaCode           = self::FIELD_NOT_SUPPORTED;
						$this->result->weatherStationCode = self::FIELD_NOT_SUPPORTED;
						$this->result->weatherStationName = self::FIELD_NOT_SUPPORTED;
						$this->result->mcc                = self::FIELD_NOT_SUPPORTED;
						$this->result->mnc                = self::FIELD_NOT_SUPPORTED;
						$this->result->mobileCarrierName  = self::FIELD_NOT_SUPPORTED;
						$this->result->elevation          = self::FIELD_NOT_SUPPORTED;
						$this->result->usageType          = self::FIELD_NOT_SUPPORTED;

						if ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->countryCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']]), '32'),
								'string',
								true
							);

							$this->result->countryName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']]), '32') + 3,
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->regionName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->cityName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']] != 0)
						{
							$this->result->latitude = $this->readByte(
								$pointer + 4 * ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']]),
								'float',
								true
							);
						}

						if ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']] != 0)
						{
							$this->result->longitude = $this->readByte(
								$pointer + 4 * ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']]),
								'float',
								true
							);
						}

						if ($this->columns[$keys[self::ISP - 1]][$this->database['type']] != 0)
						{
							$this->result->isp = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::ISP - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->domainName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->zipCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']] != 0)
						{
							$this->result->timeZone = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']] != 0)
						{
							$this->result->netSpeed = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->iddCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->areaCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']] != 0)
						{
							$this->result->weatherStationCode = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->weatherStationName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::MCC - 1]][$this->database['type']] != 0)
						{
							$this->result->mcc = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::MCC - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::MNC - 1]][$this->database['type']] != 0)
						{
							$this->result->mnc = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::MNC - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']] != 0)
						{
							$this->result->mobileCarrierName = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']] != 0)
						{
							$this->result->elevation = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						if ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']] != 0)
						{
							$this->result->usageType = $this->readByte(
								$this->readByte($pointer + 4 * ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']]), '32'),
								'string',
								true
							);
						}

						return $this->result;
				}

				return $return;
			}
			else
			{
				if ($ip_number < $ip_from)
				{
					$high = $mid - 1;
				}
				else
				{
					$low = $mid + 1;
				}
			}
		}
	}
}
