<?php
namespace MemberPressGeoCurrency;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the settings page.
 */
function mpgc_register_settings_page() {
    add_menu_page(
        __( 'Geo Currency Settings', 'memberpress-geo-currency' ),
        __( 'Geo Currency', 'memberpress-geo-currency' ),
        'manage_options',
        'mpgc-settings',
        __NAMESPACE__ . '\mpgc_settings_page_callback', // Add namespace here as well
        'dashicons-money-alt',
        81
    );
}
add_action('admin_menu', __NAMESPACE__ . '\mpgc_register_settings_page'); // Use namespace here

/**
 * Settings page callback.
 */
function mpgc_settings_page_callback() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'MemberPress Geo Currency Settings', 'memberpress-geo-currency' ); ?></h1>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'mpgc_settings_group' );
            do_settings_sections( 'mpgc-settings' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings and fields.
 */
function mpgc_register_settings() {
	register_setting( 'mpgc_settings_group', 'mpgc_ipinfo_api_key' );
	register_setting( 'mpgc_settings_group', 'mpgc_oer_api_key' );
	register_setting( 'mpgc_settings_group', 'mpgc_default_currency' );
	register_setting( 'mpgc_settings_group', 'mpgc_cache_duration' );

	add_settings_section(
		'mpgc_api_settings',
		__( 'API Settings', 'memberpress-geo-currency' ),
		'mpgc_api_settings_callback',
		'mpgc-settings'
	);

	add_settings_field(
		'mpgc_ipinfo_api_key',
		__( 'IPInfo API Key', 'memberpress-geo-currency' ),
		'mpgc_ipinfo_api_key_callback',
		'mpgc-settings',
		'mpgc_api_settings'
	);

	add_settings_field(
		'mpgc_oer_api_key',
		__( 'Open Exchange Rates API Key', 'memberpress-geo-currency' ),
		'mpgc_oer_api_key_callback',
		'mpgc-settings',
		'mpgc_api_settings'
	);

	add_settings_section(
		'mpgc_general_settings',
		__( 'General Settings', 'memberpress-geo-currency' ),
		'mpgc_general_settings_callback',
		'mpgc-settings'
	);

	add_settings_field(
		'mpgc_default_currency',
		__( 'Default Currency', 'memberpress-geo-currency' ),
		'mpgc_default_currency_callback',
		'mpgc-settings',
		'mpgc_general_settings'
	);

	add_settings_field(
		'mpgc_cache_duration',
		__( 'Cache Duration (hours)', 'memberpress-geo-currency' ),
		'mpgc_cache_duration_callback',
		'mpgc-settings',
		'mpgc_general_settings'
	);
}
add_action( 'admin_init', 'mpgc_register_settings' );

// Section Callbacks
function mpgc_api_settings_callback() {
	echo '<p>' . __( 'Enter your API keys below:', 'memberpress-geo-currency' ) . '</p>';
}

function mpgc_general_settings_callback() {
	echo '<p>' . __( 'Configure general settings:', 'memberpress-geo-currency' ) . '</p>';
}

// Field Callbacks
function mpgc_ipinfo_api_key_callback() {
	$ipinfo_api_key = get_option( 'mpgc_ipinfo_api_key' );
	printf(
		'<input type="text" name="mpgc_ipinfo_api_key" value="%s" class="regular-text" />',
		esc_attr( $ipinfo_api_key )
	);
	echo '<p class="description">' . __( 'Enter your IPInfo API key.', 'memberpress-geo-currency' ) . '</p>';
}

function mpgc_oer_api_key_callback() {
	$oer_api_key = get_option( 'mpgc_oer_api_key' );
	printf(
		'<input type="text" name="mpgc_oer_api_key" value="%s" class="regular-text" />',
		esc_attr( $oer_api_key )
	);
	echo '<p class="description">' . __( 'Enter your Open Exchange Rates API key.', 'memberpress-geo-currency' ) . '</p>';
}

function mpgc_default_currency_callback() {
	$default_currency = get_option( 'mpgc_default_currency', 'USD' );
	$currencies       = array(
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
		'HKD' => 'Hong Kong Dollar',
		'HUF' => 'Hungarian Forint',
		'ISK'  => 'Icelandic Króna',
		'INR'  => 'Indian Rupee',
		'IDR'  => 'Indonesian Rupiah',
		'IRR'  => 'Iranian Rial',
		'IMP'  => 'Isle of Man Pound',
		'ILS'  => 'Israeli New Shekel',
		'JMD'  => 'Jamaican Dollar',
		'JEP'  => 'Jersey Pound',
		'KZT'  => 'Kazakhstani Tenge',
		'KPW'  => 'North Korean Won',
		'KRW'  => 'South Korean Won',
		'KGS'  => 'Kyrgyzstani Som',
		'LAK'  => 'Laotian Kip',
		'LBP'  => 'Lebanese Pound',
		'LRD'  => 'Liberian Dollar',
		'MKD'  => 'Macedonian Denar',
		'MYR'  => 'Malaysian Ringgit',
		'MUR'  => 'Mauritian Rupee',
		'MXN'  => 'Mexican Peso',
		'MNT'  => 'Mongolian Tugrik',
		'MAD'  => 'Moroccan Dirham',
		'MZN'  => 'Mozambican Metical',
		'NAD'  => 'Namibian Dollar',
		'NPR'  => 'Nepalese Rupee',
		'ANG'   => 'Netherlands Antillean Guilder',
		'NZD'   => 'New Zealand Dollar',
		'NIO'   => 'Nicaraguan Córdoba',
		'NGN'   => 'Nigerian Naira',
		'NOK'   => 'Norwegian Krone',
		'OMR'   => 'Omani Rial',
		'PKR'   => 'Pakistani Rupee',
		'PAB'   => 'Panamanian Balboa',
		'PYG'   => 'Paraguayan Guarani',
		'PEN'   => 'Peruvian Sol',
		'PHP'   => 'Philippine Peso',
		'PLN'   => 'Polish Zloty',
		'QAR'   => 'Qatari Riyal',
		'RON'   => 'Romanian Leu',
		'RUB'   => 'Russian Ruble',
		'RWF'   => 'Rwandan Franc',
		'SHP'   => 'Saint Helena Pound',
		'SAR'   => 'Saudi Riyal',
		'RSD'   => 'Serbian Dinar',
		'SCR'   => 'Seychellois Rupee',
		'SGD'   => 'Singapore Dollar',
		'SBD'   => 'Solomon Islands Dollar',
		'SOS'   => 'Somali Shilling',
		'ZAR'   => 'South African Rand',
		'LKR'   => 'Sri Lankan Rupee',
		'SEK'   => 'Swedish Krona',
		'CHF'   => 'Swiss Franc',
		'SRD'   => 'Surinamese Dollar',
		'SYP'   => 'Syrian Pound',
		'TWD'   => 'Taiwanese New Dollar',
		'TZS'   => 'Tanzanian Shilling',
		'THB'   => 'Thai Baht',
		'TTD'   => 'Trinidad and Tobago Dollar',
		'TND'   => 'Tunisian Dinar',
		'TRY'   => 'Turkish Lira',
		'TMT'    => 'Turkmenistani Manat',
		'UGX'    => 'Ugandan Shilling',
		'UAH'    => 'Ukrainian Hryvnia',
		'AED'    => 'United Arab Emirates Dirham',
		'GBP'    => 'British Pound Sterling',
		'USD'    => 'United States Dollar',
		'UYU'    => 'Uruguayan Peso',
		'UZS'    => 'Uzbekistani Som',
		'VUV'    => 'Vanuatu Vatu',
		'VES'    => 'Venezuelan Bolívar',
		'VND'    => 'Vietnamese Dong',
		'XPF'    => 'Wallis and Futuna CFP Franc',
		'YER'    => 'Yemeni Rial',
		'ZMW'    => 'Zambian Kwacha',
	);
	echo "<select name='mpgc_default_currency'>";
	foreach ( $currencies as $code => $name ) {
		printf(
			'<option value="%s" %s>%s - %s</option>',
			esc_attr( $code ),
			selected( $default_currency, $code, false ),
			esc_html( $code ),
			esc_html( $name )
		);
	}
	echo "</select>";
	echo ' <span id="mpgc_default_currency_symbol"></span>';
	echo '<p class="description">' . __( 'Select the default currency to use.', 'memberpress-geo-currency' ) . '</p>';
}

function mpgc_cache_duration_callback() {
	$cache_duration = get_option( 'mpgc_cache_duration', 24 );
	printf(
		'<input type="number" name="mpgc_cache_duration" value="%s" min="1" class="small-text" />',
		esc_attr( $cache_duration )
	);
	echo '<p class="description">' . __( 'Enter the cache duration in hours.', 'memberpress-geo-currency' ) . '</p>';
}