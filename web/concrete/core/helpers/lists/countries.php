<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Grabs a list of countries commonly used in web forms.
 * First looks in Config table; if not there gets from class data. This enables other software
 * to manipulate/modify the list.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Lists_Countries {

	protected $countries = array(
	'AD' => 'Andorra',
	'AE' => 'United Arab Emirates',
	'AF' => 'Afghanistan',
	'AG' => 'Antigua and Barbuda',
	'AI' => 'Anguilla',
	'AL' => 'Albania',
	'AM' => 'Armenia',
	'AN' => 'Netherlands Antilles',
	'AO' => 'Angola',
	'AQ' => 'Antarctica',
	'AR' => 'Argentina',
	'AS' => 'American Samoa',
	'AT' => 'Austria',
	'AU' => 'Australia',
	'AW' => 'Aruba',
	'AZ' => 'Azerbaijan',
	'BA' => 'Bosnia and Herzegovina',
	'BB' => 'Barbados',
	'BD' => 'Bangladesh',
	'BE' => 'Belgium',
	'BF' => 'Burkina Faso',
	'BG' => 'Bulgaria',
	'BH' => 'Bahrain',
	'BI' => 'Burundi',
	'BJ' => 'Benin',
	'BM' => 'Bermuda',
	'BN' => 'Brunei Darussalam',
	'BO' => 'Bolivia',
	'BR' => 'Brazil',
	'BS' => 'Bahamas',
	'BT' => 'Bhutan',
	'BV' => 'Bouvet Island',
	'BW' => 'Botswana',
	'BY' => 'Belarus',
	'BZ' => 'Belize',
	'CA' => 'Canada',
	'CC' => 'Cocos (Keeling) Islands',
	'CF' => 'Central African Republic',
	'CG' => 'Congo',
	'CH' => 'Switzerland',
	'CI' => 'Cote D\'Ivoire (Ivory Coast)',
	'CK' => 'Cook Islands',
	'CL' => 'Chile',
	'CM' => 'Cameroon',
	'CN' => 'China',
	'CO' => 'Colombia',
	'CR' => 'Costa Rica',
	'SRB' => 'Serbia',
	'MNE' => 'Montenegro',
	'CU' => 'Cuba',
	'CV' => 'Cape Verde',
	'CX' => 'Christmas Island',
	'CY' => 'Cyprus',
	'CZ' => 'Czech Republic',
	'DE' => 'Germany',
	'DJ' => 'Djibouti',
	'DK' => 'Denmark',
	'DM' => 'Dominica',
	'DO' => 'Dominican Republic',
	'DZ' => 'Algeria',
	'EC' => 'Ecuador',
	'EE' => 'Estonia',
	'EG' => 'Egypt',
	'EH' => 'Western Sahara',
	'ER' => 'Eritrea',
	'ES' => 'Spain',
	'ET' => 'Ethiopia',
	'FI' => 'Finland',
	'FJ' => 'Fiji',
	'FK' => 'Falkland Islands (Malvinas)',
	'FM' => 'Micronesia',
	'FO' => 'Faroe Islands',
	'FR' => 'France',
	'FX' => 'France, Metropolitan',
	'GA' => 'Gabon',
	'GB' => 'Great Britain (UK)',
	'GD' => 'Grenada',
	'GE' => 'Georgia',
	'GF' => 'French Guiana',
	'GH' => 'Ghana',
	'GI' => 'Gibraltar',
	'GL' => 'Greenland',
	'GM' => 'Gambia',
	'GN' => 'Guinea',
	'GP' => 'Guadeloupe',
	'GQ' => 'Equatorial Guinea',
	'GR' => 'Greece',
	'GS' => 'S. Georgia and S. Sandwich Isls.',
	'GT' => 'Guatemala',
	'GU' => 'Guam',
	'GW' => 'Guinea-Bissau',
	'GY' => 'Guyana',
	'HK' => 'Hong Kong',
	'HM' => 'Heard and McDonald Islands',
	'HN' => 'Honduras',
	'HR' => 'Croatia (Hrvatska)',
	'HT' => 'Haiti',
	'HU' => 'Hungary',
	'ID' => 'Indonesia',
	'IE' => 'Ireland',
	'IL' => 'Israel',
	'IN' => 'India',
	'IO' => 'British Indian Ocean Territory',
	'IQ' => 'Iraq',
	'IR' => 'Iran',
	'IS' => 'Iceland',
	'IT' => 'Italy',
	'JM' => 'Jamaica',
	'JO' => 'Jordan',
	'JP' => 'Japan',
	'KE' => 'Kenya',
	'KG' => 'Kyrgyzstan',
	'KH' => 'Cambodia',
	'KI' => 'Kiribati',
	'KM' => 'Comoros',
	'KN' => 'Saint Kitts and Nevis',
	'KP' => 'Korea (North)',
	'KR' => 'Korea (South)',
	'KW' => 'Kuwait',
	'KY' => 'Cayman Islands',
	'KZ' => 'Kazakhstan',
	'LA' => 'Laos',
	'LB' => 'Lebanon',
	'LC' => 'Saint Lucia',
	'LI' => 'Liechtenstein',
	'LK' => 'Sri Lanka',
	'LR' => 'Liberia',
	'LS' => 'Lesotho',
	'LT' => 'Lithuania',
	'LU' => 'Luxembourg',
	'LV' => 'Latvia',
	'LY' => 'Libya',
	'MA' => 'Morocco',
	'MC' => 'Monaco',
	'MD' => 'Moldova',
	'MG' => 'Madagascar',
	'MH' => 'Marshall Islands',
	'MK' => 'Macedonia',
	'ML' => 'Mali',
	'MM' => 'Myanmar',
	'MN' => 'Mongolia',
	'MO' => 'Macau',
	'MP' => 'Northern Mariana Islands',
	'MQ' => 'Martinique',
	'MR' => 'Mauritania',
	'MS' => 'Montserrat',
	'MT' => 'Malta',
	'MU' => 'Mauritius',
	'MV' => 'Maldives',
	'MW' => 'Malawi',
	'MX' => 'Mexico',
	'MY' => 'Malaysia',
	'MZ' => 'Mozambique',
	'NA' => 'Namibia',
	'NC' => 'New Caledonia',
	'NE' => 'Niger',
	'NF' => 'Norfolk Island',
	'NG' => 'Nigeria',
	'NI' => 'Nicaragua',
	'NL' => 'Netherlands',
	'NO' => 'Norway',
	'NP' => 'Nepal',
	'NR' => 'Nauru',
	'NT' => 'Neutral Zone',
	'NU' => 'Niue',
	'NZ' => 'New Zealand (Aotearoa)',
	'OM' => 'Oman',
	'PA' => 'Panama',
	'PE' => 'Peru',
	'PF' => 'French Polynesia',
	'PG' => 'Papua New Guinea',
	'PH' => 'Philippines',
	'PK' => 'Pakistan',
	'PL' => 'Poland',
	'PM' => 'St. Pierre and Miquelon',
	'PN' => 'Pitcairn',
	'PR' => 'Puerto Rico',
	'PS' => 'Palestine',
	'PT' => 'Portugal',
	'PW' => 'Palau',
	'PY' => 'Paraguay',
	'QA' => 'Qatar',
	'RE' => 'Reunion',
	'RO' => 'Romania',
	'RU' => 'Russian Federation',
	'RW' => 'Rwanda',
	'SA' => 'Saudi Arabia',
	'SB' => 'Solomon Islands',
	'SC' => 'Seychelles',
	'SD' => 'Sudan',
	'SE' => 'Sweden',
	'SG' => 'Singapore',
	'SH' => 'St. Helena',
	'SI' => 'Slovenia',
	'SJ' => 'Svalbard and Jan Mayen Islands',
	'SK' => 'Slovak Republic',
	'SL' => 'Sierra Leone',
	'SM' => 'San Marino',
	'SN' => 'Senegal',
	'SO' => 'Somalia',
	'SR' => 'Suriname',
	'ST' => 'Sao Tome and Principe',
	'SU' => 'USSR (former)',
	'SV' => 'El Salvador',
	'SY' => 'Syria',
	'SZ' => 'Swaziland',
	'TC' => 'Turks and Caicos Islands',
	'TD' => 'Chad',
	'TF' => 'French Southern Territories',
	'TG' => 'Togo',
	'TH' => 'Thailand',
	'TJ' => 'Tajikistan',
	'TK' => 'Tokelau',
	'TM' => 'Turkmenistan',
	'TN' => 'Tunisia',
	'TO' => 'Tonga',
	'TP' => 'East Timor',
	'TR' => 'Turkey',
	'TT' => 'Trinidad and Tobago',
	'TV' => 'Tuvalu',
	'TW' => 'Taiwan',
	'TZ' => 'Tanzania',
	'UA' => 'Ukraine',
	'UG' => 'Uganda',
	'UK' => 'United Kingdom',
	'UM' => 'US Minor Outlying Islands',
	'US' => 'United States',
	'UY' => 'Uruguay',
	'UZ' => 'Uzbekistan',
	'VA' => 'Vatican City State (Holy See)',
	'VC' => 'Saint Vincent and the Grenadines',
	'VE' => 'Venezuela',
	'VG' => 'Virgin Islands (British)',
	'VI' => 'Virgin Islands (U.S.)',
	'VN' => 'Viet Nam',
	'VU' => 'Vanuatu',
	'WF' => 'Wallis and Futuna Islands',
	'WS' => 'Samoa',
	'YE' => 'Yemen',
	'YT' => 'Mayotte',
	'ZA' => 'South Africa',
	'ZM' => 'Zambia',
	'ZR' => 'Zaire',
	'ZW' => 'Zimbabwe'
	);
	
	/**
	 * Returns an array of Countries with their short name as the key and their full name as the value.
	 * First looks in Config table; if not there gets from class data. This enables other software
	 * to manipulate/modify the list.
	 * @return array
	 */
	public function getCountries() {
		$co = new Config();

		// First look in Config data
		$ct_ser_list = $co->get('COUNTRIES_LIST');
		if (!empty($ct_ser_list)){
			$clist = unserialize($ct_ser_list);
			if (is_array($clist)){
				return $clist;
			}
		}

		// Else use defaults and copy to Config data
		asort($this->countries);
		$co->save('COUNTRIES_LIST', serialize($this->countries));
		return $this->countries;
	}

	/**
	 * Gets a country full name given its index
	 * @return string
	 * @param string $index
	 */
	public function getCountryName($index) {
		$clist = $this->getCountries();
		return $clist[$index];
	}
}