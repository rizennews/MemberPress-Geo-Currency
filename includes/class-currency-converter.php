<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MPGC_Currency_Converter {
    private $geolocation;
    private $oer_api_key;
    private $base_url = 'https://openexchangerates.org/api/latest.json';
    private $cache_key = 'mpgc_exchange_rates';
    private $cache_duration;

    public function __construct( MPGC_Geolocation $geolocation ) {
        $this->geolocation = $geolocation;
        $this->oer_api_key = get_option( 'mpgc_oer_api_key' );
        $this->cache_duration = get_option( 'mpgc_cache_duration', 24 ); // Default 24 hours
    }

    /**
     * Get exchange rates from Open Exchange Rates API or cache.
     */
    public function get_exchange_rates() {
        $rates = get_transient( $this->cache_key );

        if ( false === $rates ) {
            $response = wp_remote_get( add_query_arg(
                array(
                    'app_id' => $this->oer_api_key,
                    'base'   => 'USD', // OER Free plan only allows USD as the base
                ),
                $this->base_url
            ) );

            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                return false; // Handle error or return cached/default rates
            }

            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );

            $rates = $data['rates'] ?? false; // Simplified check

            if ( false !== $rates ) {
                set_transient( $this->cache_key, $rates, $this->cache_duration * HOUR_IN_SECONDS );
            } else {
                return false; // Handle error or return cached/default rates
            }
        }

        return $rates;
    }

    /**
     * Convert price to the target currency.
     */
    public function convert_price( $price, $from_currency, $to_currency ) {
        $rates = $this->get_exchange_rates();

        if ( ! $rates || ! isset( $rates[ $from_currency ] ) || ! isset( $rates[ $to_currency ] ) ) {
            return $price; // Return the original price if no rates are found or invalid currencies
        }

        // Convert to USD (since OER Free plan is based on USD)
        $price_in_usd = $price / $rates[ $from_currency ];
        // Convert from USD to target currency
        $converted_price = $price_in_usd * $rates[ $to_currency ];

        return round( $converted_price, 2 ); // Round to 2 decimal places
    }

    /**
     * Convert and format the price for display.
     */
    public function convert_price_display( $price_str, $product ) {
        // Get user's country code
        $country_code = $this->geolocation->get_user_country();

        // Get default currency from settings
        $default_currency = get_option( 'mpgc_default_currency', 'USD' );

        // Determine the target currency based on the user's country
        $target_currency = $this->get_currency_by_country( $country_code, $default_currency );

        // Get the original currency from the product (assuming you have a way to store/retrieve this)
        // For demonstration, let's assume you have a custom field 'product_currency'
        $original_currency = get_post_meta($product->ID, 'product_currency', true);
        if( empty( $original_currency ) ) {
            $original_currency = $default_currency;
        }
            
        // Extract the numeric price from the string
        // Assumes $price_str format is like "$10.00" or "10.00 USD"
        preg_match('/\d+\.?\d*/', $price_str, $matches);
        if (empty($matches)) {
            return $price_str; // Return original string if no numeric price is found
        }
        $price = floatval($matches[0]);

        // Convert the price
        $converted_price = $this->convert_price( $price, $original_currency, $target_currency );

        // Format the converted price with the target currency
        $formatted_price = $this->format_price( $converted_price, $target_currency );

        return $formatted_price;
    }

    /**
     * Get currency code based on country code.
     */
    public function get_currency_by_country($country_code, $default_currency) {
        // Mapping of country codes to currency codes
        $mapping = array(
'USD' => 'US Dollar',
'EUR' => 'Euro',
'GBP' => 'British Pound',
'GHS' => 'Ghanaian Cedis',
'JPY' => 'Japanese Yen',
'AUD' => 'Australian Dollar',
'CAD' => 'Canadian Dollar',
'ALL' => 'Albanian Lek',
'AFN' => 'Afghan Afghani',
'ARS' => 'Argentine Peso',
'AWG' => 'Aruban Florin',
'AZN' => 'Azerbaijani Manat',
'BSD' => 'Bahamian Dollar',
'BHD' => 'Bahraini Dinar',
'BDT' => 'Bangladeshi Taka',
'BBD' => 'Barbadian Dollar',
'BYN' => 'Belarusian Ruble',
'BZD' => 'Belize Dollar',
'BMD' => 'Bermudian Dollar',
'BOB' => 'Bolivian Boliviano',
'BAM' => 'Bosnian Convertible Mark',
'BWP' => 'Botswana Pula',
'BGN' => 'Bulgarian Lev',
'BRL' => 'Brazilian Real',
'BND' => 'Brunei Dollar',
'KHR' => 'Cambodian Riel',
'CAD' => 'Canadian Dollar',
'KYD' => 'Cayman Islands Dollar',
'CLP' => 'Chilean Peso',
'CNY' => 'Chinese Yuan Renminbi',
'COP' => 'Colombian Peso',
'CRC' => 'Costa Rican Colón',
'CUP' => 'Cuban Peso',
'CZK' => 'Czech Koruna',
'DKK' => 'Danish Krone',
'DOP' => 'Dominican Peso',
'ECD' => 'East Caribbean Dollar',
'EGP' => 'Egyptian Pound',
'SVC' => 'El Salvador Colón',
'EUR' => 'Euro',
'HKD' => 'Hong Kong Dollar',
'HUF' => 'Hungarian Forint',
'ISK'  =>'Icelandic Króna',
'INR'  =>'Indian Rupee',
'IDR'  =>'Indonesian Rupiah',
'IRR'  =>'Iranian Rial',
'IMP'  =>'Isle of Man Pound',
'ILS'  =>'Israeli New Shekel',
'JMD'  =>'Jamaican Dollar',
'JPY'  =>'Japanese Yen',
'JEP'  =>'Jersey Pound',
'KZT'  =>'Kazakhstani Tenge',
'KPW'  =>'North Korean Won',
'KRW'  =>'South Korean Won',
'KGS'  =>'Kyrgyzstani Som',
'LAK'  =>'Laotian Kip',
'LBP'  =>'Lebanese Pound',
'LRD'  =>'Liberian Dollar',
'MKD'  =>'Macedonian Denar',
'MYR'  =>'Malaysian Ringgit',
'MUR'  =>'Mauritian Rupee',
'MXN'  =>'Mexican Peso',
'MNT'  =>'Mongolian Tugrik',
'MAD'  =>'Moroccan Dirham',
'MZN'  =>'Mozambican Metical',
'NAD'  =>'Namibian Dollar',
'NPR'  =>'Nepalese Rupee',
'ANG'   =>'Netherlands Antillean Guilder',
'NZD'   =>'New Zealand Dollar',
'NIO'   =>'Nicaraguan Córdoba',
'NGN'   =>'Nigerian Naira',
'NOK'   =>'Norwegian Krone',
'OMR'   =>'Omani Rial',
'PKR'   =>'Pakistani Rupee',
'PAB'   =>'Panamanian Balboa',
'PYG'   =>'Paraguayan Guarani',
'PEN'   =>'Peruvian Sol',
'PHP'   =>'Philippine Peso',
'PLN'   =>'Polish Zloty',
'QAR'   =>'Qatari Riyal',
'RON'   =>'Romanian Leu',
'RUB'   =>'Russian Ruble',
'RWF'   =>'Rwandan Franc',
'SHP'   =>'Saint Helena Pound',
'SAR'   =>'Saudi Riyal',
'RSD'   =>'Serbian Dinar',
'SCR'   =>'Seychellois Rupee',
'SGD'   =>'Singapore Dollar',
'SBD'   =>'Solomon Islands Dollar',
'SOS'   =>'Somali Shilling',
'ZAR'   =>'South African Rand',
'LKR'   =>'Sri Lankan Rupee',
'SEK'   =>'Swedish Krona',
'CHF'   =>'Swiss Franc',
'SRD'   =>'Surinamese Dollar',
'SYP'   =>'Syrian Pound',
'TWD'   =>'Taiwanese New Dollar',
'TZS'   =>'Tanzanian Shilling',
'THB'   =>'Thai Baht',
'TTD'   =>'Trinidad and Tobago Dollar',
'TND'   =>'Tunisian Dinar',
'TRY'   =>'Turkish Lira',
'TMT'    => 'Turkmenistani Manat',
 'UGX'    => 'Ugandan Shilling',
 'UAH'    => 'Ukrainian Hryvnia',
 'AED'    => 'United Arab Emirates Dirham',
 'GBP'    => 'British Pound Sterling',
 'USD'    => 'United States Dollar',
 'UYU'    => 'Uruguayan Peso',
 'UZS'    => 'Uzbekistani Som',
 'VUV'    => 'Vanuatu Vatu',
 'EUR'    => 'Vatican City Euro',
 'VES'    => 'Venezuelan Bolívar',
 'VND'    => 'Vietnamese Dong',
 'XPF'    => 'Wallis and Futuna CFP Franc',
 'YER'    => 'Yemeni Rial',
 'ZMW'    => 'Zambian Kwacha',
        );

        return $mapping[ strtoupper( $country_code ) ] ?? $default_currency;
    }

    /**
     * Format the price with the appropriate currency symbol and format.
     */
    public function format_price($price, $currency_code) {
        // Use NumberFormatter for locale-aware formatting
        $formatter = new NumberFormatter( $this->get_locale_by_country( $this->geolocation->get_user_country() ), NumberFormatter::CURRENCY );

        return $formatter->formatCurrency($price, $currency_code);
    }

    // Optional: Helper function to get locale by country (you might need to expand this)
    private function get_locale_by_country( $country_code ) {
        $locales = array(
            'US' => 'en_US',
            'CA' => 'en_CA',
            'GB' => 'en_GB',
            'AU' => 'en_AU',
            'DE' => 'de_DE',
            'FR' => 'fr_FR',
            'ES' => 'es_ES',
            'AF' => 'fa_AF', // Afghanistan
            'AL' => 'sq_AL', // Albania
            'DZ' => 'ar_DZ', // Algeria
            'AD' => 'ca_AD', // Andorra
            'AO' => 'pt_AO', // Angola
            'AG' => 'en_AG', // Antigua and Barbuda
            'AR' => 'es_AR', // Argentina
            'AM' => 'hy_AM', // Armenia
            'AZ' => 'az_AZ', // Azerbaijan
            'BS' => 'en_BS', // Bahamas
            'BH' => 'ar_BH', // Bahrain
            'BD' => 'bn_BD', // Bangladesh
            'BB' => 'en_BB', // Barbados
            'BY' => 'be_BY', // Belarus
            'BE' => 'nl_BE', // Belgium
            'BZ' => 'en_BZ', // Belize
            'BJ' => 'fr_BJ', // Benin
            'BT' => 'dz_BT', // Bhutan
            'BO' => 'es_BO', // Bolivia
            'BA' => 'bs_BA', // Bosnia and Herzegovina
            'BW'  => 'en_BW', // Botswana
            'BR'  => 'pt_BR', // Brazil
            'BN'  => 'ms_BN', // Brunei
            'BG'  => 'bg_BG', // Bulgaria
            'BF'  => 'fr_BF', // Burkina Faso
            'BI'  => 'rn_BI', // Burundi
            'CV'  => 'pt_CV', // Cabo Verde
            'KH'  => 'km_KH', // Cambodia
            'CM'  => 'fr_CM', // Cameroon
            'CA'  => 'en_CA', // Canada
            'CF'  => 'fr_CF', // Central African Republic
            'TD'  => 'fr_TD', // Chad
            'CL'  => 'es_CL', // Chile
            'CN'  => 'zh_CN', // China
            'CO'  => 'es_CO', // Colombia
            'KM'  => 'ar_KM', // Comoros
            'CD'  => 'fr_CD', // Congo, Democratic Republic of the
            'CG'  => 'fr_CG', // Congo, Republic of the
            'CR'  => 'es_CR', // Costa Rica
            'CI'  => 'fr_CI', // Cote d'Ivoire
            'HR'  => 'hr_HR', // Croatia
            'CU'  => 'es_CU', // Cuba
            'CY'  => 'el_CY', // Cyprus
            'CZ'  => 'cs_CZ', // Czechia
            'DK'  => 'da_DK', // Denmark
            'DJ'  => 'fr_DJ', // Djibouti
            'DM'  => 'en_DM', // Dominica
            'DO'  => 'es_DO', // Dominican Republic
            'EC'  => 'es_EC', // Ecuador
            'EG'  => 'ar_EG', // Egypt
            'SV'  => 'es_SV', // El Salvador
            'GQ'  => 'es_GQ', // Equatorial Guinea
            'ER'  => 'ti_ER', // Eritrea
            'EE'  => 'et_EE', // Estonia
            'SZ'  =>'en_SZ',// Eswatini
            'ET'   =>'am_ET',// Ethiopia
            'FJ'   =>'en_FJ',// Fiji
            'FI'   =>'fi_FI',// Finland
            'FR'   =>'fr_FR',// France
            'GA'   =>'fr_GA',// Gabon
            'GM'   =>'en_GM',// Gambia
            'GE'   =>'ka_GE',// Georgia
            'DE'   =>'de_DE',// Germany
            'GH'   =>'ak_GH',// Ghana
            'GR'   =>'el_GR',// Greece
            'GD'   =>'en_GD',// Grenada
            'GT'   =>'es_GT',// Guatemala
            'GN'   =>'fr_GN',// Guinea
            'GW'   =>'pt_GW',// Guinea-Bissau
            'GY'   =>'en_GY',// Guyana
            'HT'   =>'ht_HT',// Haiti
            'HN'   =>'es_HN',// Honduras
            'HU'   =>'hu_HU',// Hungary
            'IS'   =>'is_IS',// Iceland
            'IN'   =>'hi_IN',// India
            'ID'   =>'id_ID',// Indonesia
            'IR'   =>'fa_IR',// Iran
            'IQ'   =>'ar_IQ',// Iraq
            'IE'   =>'en_IE',// Ireland
            'IL'   =>'he_IL',// Israel
            'IT'   =>'it_IT',// Italy
            'JM'   =>'en_JM',// Jamaica
            'JP'   =>'ja_JP',// Japan
            'JO'   =>'ar_JO',// Jordan
            'KZ'   =>'kk_KZ',// Kazakhstan
            'KE'   =>'sw_KE',// Kenya
            'KI'    =>'en_KI',    //'Kiribati'
            'XK'    =>'sq_XK',    //'Kosovo'
            'KW'    =>'ar_KW',    //'Kuwait'
            'KG'    =>'ky_KG',    //'Kyrgyzstan'
            'LA'    =>'lo_LA',    //'Laos'
            'LV'    =>'lv_LV',    //'Latvia'
            'LB'    =>'ar_LB',    //'Lebanon'
            'LS'    =>'en_LS',    //'Lesotho'
            'LR'    =>'en_LR',    //'Liberia'
            'LY'    =>'ar_LY',    //'Libya'
            'LI'    =>'de_LI',    //'Liechtenstein'
            'LT'    =>'lt_LT',    //'Lithuania'
            'LU'    =>'lb_LU',    //'Luxembourg'
            'MG'    =>'mg_MG',    //'Madagascar'
            'MW'    =>'ny_MW',    //'Malawi'
            'MY'    =>'ms_MY',    //'Malaysia'
            'MV'    =>'dv_MV',    //'Maldives'
            'ML'     => 'fr_ML',     //'Mali’
            'MT'     => 'mt_MT',     //'Malta’
            'MH'     => 'en_MH',     //'Marshall Islands’
            'MR'     => 'ar_MR',     //'Mauritania’
            'MU'     => 'en_MU',     //'Mauritius’
            'MX'     => 'es_MX',     //'Mexico’
            'FM'     => 'en_FM',     //'Micronesia’
            'MD'     => 'ro_MD',     //'Moldova’
            'MC'     => 'fr_MC',     //'Monaco’
            'MN'     => 'mn_MN',     //'Mongolia’
            'ME'     => 'sr_ME',     //'Montenegro’
            'MA'     => 'ar_MA',     //'Morocco’
            'MZ'     => 'pt_MZ',     //'Mozambique’
            'MM'      => 'my_MM',      //'Myanmar’
            'NA'      => 'af_NA',      //'Namibia’
            'NR'      => 'na_NR',      //'Nauru’
            'NP'      => 'ne_NP',      //'Nepal’
            'NL'      => 'nl_NL',      //'Netherlands’
            'NZ'      => 'en_NZ',      //'New Zealand’
            'NI'      => 'es_NI',      //'Nicaragua’
            'NE'      => 'fr_NE',      //'Niger’
            'NG'      => 'en_NG',      //'Nigeria’
            'KP'      => 'ko_KP',      //'North Korea’
            'MK'      => 'mk_MK',      //'North Macedonia’
            'NO'       => 'no_NO',       //(Norway)
            'OM'       => 'ar_OM',       //(Oman)
            'PK'       => 'ur_PK',       //(Pakistan)
            'PW'       => 'en_PW',       //(Palau)
            'PS'       => 'ar_PS',       //(Palestine)
            'PA'       => 'es_PA',       //(Panama)
            'PG'       => 'en_PG',       //(Papua New Guinea)
            'PY'       => 'es_PY',       //(Paraguay)
            'PE'       => 'es_PE',       //(Peru)
            'PH'       => 'tl_PH',       //(Philippines)
            'PL'       => 'pl_PL',       //(Poland)
            'PT'       => 'pt_PT',       //(Portugal)
            'QA'        =>'ar_QA',        //(Qatar)
            'RO'        =>'ro_RO',        //(Romania)
            'RU'        =>'ru_RU',        //(Russia)
            'RW'        =>'rw_RW',        //(Rwanda)
            'KN'        =>'en_KN',        //(Saint Kitts and Nevis)
            'LC'        =>'en_LC',        //(Saint Lucia)
            'VC'        =>'en_VC',        //(Saint Vincent and the Grenadines)
            'WS'        =>'sm_WS',        //(Samoa)
            'SM'        =>'it_SM',        //(San Marino)
            'ST'        =>'pt_ST',        //(Sao Tome and Principe)
            'SA'        =>'ar_SA',        //(Saudi Arabia)
            'SN'        =>'fr_SN',        //(Senegal)
            'RS'        =>'sr_RS',        //(Serbia)
            'SC'        =>'cr_SC',        //(Seychelles)
            'SL'         =>'en_SL',         //(Sierra Leone)
            'SG'         =>'en_SG',         //(Singapore)
            'SK'         =>'sk_SK',         //(Slovakia)
            'SI'         =>'sl_SI',         //(Slovenia)
            'SB'         =>'en_SB',         //(Solomon Islands)
            'SO'         =>'so_SO',         //(Somalia)
            'ZA'         =>'af_ZA',         //(South Africa)
            'KR'         =>'ko_KR',         //(South Korea)
            'SS'         =>'en_SS',         //(South Sudan )
            'ES'         =>'es_ES',         //(Spain )
            'LK'          =>'si_LK',          //(Sri Lanka )
            'SD'          =>'ar_SD',          //(Sudan )
            'SR'          =>'nl_SR',          //(Suriname )
            'SE'          =>'sv_SE',          //(Sweden )
            'CH'          =>'de_CH',          //(Switzerland )
            'SY'          =>'ar_SY',          //(Syria )
            'TW'          =>'zh_TW',          //(Taiwan )
            'TJ'          =>'tg_TJ',          //(Tajikistan )
            'TZ'          =>'sw_TZ',          //(Tanzania )
            'TH'          =>'th_TH',           //(Thailand )
            'TL'           =>'pt_TL',           //(Timor-Leste )
            'TG'           =>'fr_TG',           //(Togo )
            'TO'           =>'to_TO',           //(Tonga )
            'TT'           =>'en_TT',           //(Trinidad and Tobago )
            'TN'           =>'ar_TN',           //(Tunisia )
            'TR'           =>'tr_TR',           //(Turkey )
            'TM'           =>'tk_TM',           //(Turkmenistan )
            'TV'           =>'en_TV',           //(Tuvalu )
            'UG'           =>'lg_UG',           //(Uganda )
            'UA'           =>'uk_UA',           //(Ukraine )
            'AE'           =>'ar_AE',           //(United Arab Emirates (UAE ))
            'GB'           =>'en_GB',           //(United Kingdom (UK ))
            'US'           =>'en_US',           //(United States of America (USA ))
            'UY'           =>'es_UY',           //(Uruguay )
            'UZ'           =>'uz_UZ',           //(Uzbekistan )
            'VU'            =>'bi_VU',            //'Vanuatu'
            'VA'            =>'la_VA',            //'Vatican City (Holy See)'
            'VE'            =>'es_VE',            //'Venezuela'
            'VN'            =>'vi_VN',            //'Vietnam'
            'YE'            =>'ar_YE',            //'Yemen'
            'ZM'            =>'en_ZM',            //'Zambia'
            'ZW'            =>'en_ZW',            //'Zimbabwe'
        );

        return $locales[ strtoupper( $country_code ) ] ?? 'en_US'; // Default to en_US
    }
}