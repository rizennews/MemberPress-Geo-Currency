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
        __NAMESPACE__ . '\mpgc_settings_page_callback', // Use namespace
        'dashicons-money-alt',
        100
    );
}
add_action('admin_menu', __NAMESPACE__ . '\mpgc_register_settings_page'); // Use namespace

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
        __NAMESPACE__ . '\mpgc_api_settings_callback', // Use namespace
        'mpgc-settings'
    );

    add_settings_field(
        'mpgc_ipinfo_api_key',
        __( 'IPInfo API Key', 'memberpress-geo-currency' ),
        __NAMESPACE__ . '\mpgc_ipinfo_api_key_callback', // Use namespace
        'mpgc-settings',
        'mpgc_api_settings'
    );

    add_settings_field(
        'mpgc_oer_api_key',
        __( 'Open Exchange Rates API Key', 'memberpress-geo-currency' ),
        __NAMESPACE__ . '\mpgc_oer_api_key_callback', // Use namespace
        'mpgc-settings',
        'mpgc_api_settings'
    );

    add_settings_section(
        'mpgc_general_settings',
        __( 'General Settings', 'memberpress-geo-currency' ),
        __NAMESPACE__ . '\mpgc_general_settings_callback', // Use namespace
        'mpgc-settings'
    );

    add_settings_field(
        'mpgc_default_currency',
        __( 'Default Currency', 'memberpress-geo-currency' ),
        __NAMESPACE__ . '\mpgc_default_currency_callback', // Use namespace
        'mpgc-settings',
        'mpgc_general_settings'
    );

    add_settings_field(
        'mpgc_cache_duration',
        __( 'Cache Duration (hours)', 'memberpress-geo-currency' ),
        __NAMESPACE__ . '\mpgc_cache_duration_callback', // Use namespace
        'mpgc-settings',
        'mpgc_general_settings'
    );
}
add_action( 'admin_init', __NAMESPACE__ . '\mpgc_register_settings' ); // Use namespace

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
    echo '<p class="description">';
    printf(
        __( 'Enter your IPInfo API key. Get one at %s.', 'memberpress-geo-currency' ),
        '<a href="https://ipinfo.io/signup" target="_blank">ipinfo.io</a>'
    );
    echo '</p>';
}

function mpgc_oer_api_key_callback() {
    $oer_api_key = get_option( 'mpgc_oer_api_key' );
    printf(
        '<input type="text" name="mpgc_oer_api_key" value="%s" class="regular-text" />',
        esc_attr( $oer_api_key )
    );
    echo '<p class="description">';
    printf(
        __( 'Enter your Open Exchange Rates API key. Get one at %s.', 'memberpress-geo-currency' ),
        '<a href="https://openexchangerates.org/signup" target="_blank">openexchangerates.org</a>'
    );
    echo '</p>';
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
        'ISK' => 'Icelandic Króna',
        'INR' => 'Indian Rupee',
        'IDR' => 'Indonesian Rupiah',
        'IRR' => 'Iranian Rial',
        'IMP' => 'Isle of Man Pound',
        'ILS' => 'Israeli New Shekel',
        'JMD' => 'Jamaican Dollar',
        'JEP' => 'Jersey Pound',
        'KZT' => 'Kazakhstani Tenge',
        'KPW' => 'North Korean Won',
        'KRW' => 'South Korean Won',
        'KGS' => 'Kyrgyzstani Som',
        'LAK' => 'Laotian Kip',
        'LBP' => 'Lebanese Pound',
        'LRD' => 'Liberian Dollar',
        'MKD' => 'Macedonian Denar',
        'MYR' => 'Malaysian Ringgit',
        'MUR' => 'Mauritian Rupee',
        'MXN' => 'Mexican Peso',
        'MNT' => 'Mongolian Tugrik',
        'MAD' => 'Moroccan Dirham',
        'MZN' => 'Mozambican Metical',
        'NAD' => 'Namibian Dollar',
        'NPR' => 'Nepalese Rupee',
        'ANG' => 'Netherlands Antillean Guilder',
        'NZD' => 'New Zealand Dollar',
        'NIO' => 'Nicaraguan Córdoba',
        'NGN' => 'Nigerian Naira',
        'NOK' => 'Norwegian Krone',
        'OMR' => 'Omani Rial',
        'PKR' => 'Pakistani Rupee',
        'PAB' => 'Panamanian Balboa',
        'PYG' => 'Paraguayan Guarani',
        'PEN' => 'Peruvian Sol',
        'PHP' => 'Philippine Peso',
        'PLN' => 'Polish Zloty',
        'QAR' => 'Qatari Riyal',
        'RON' => 'Romanian Leu',
        'RUB' => 'Russian Ruble',
        'RWF' => 'Rwandan Franc',
        'SHP' => 'Saint Helena Pound',
        'SAR' => 'Saudi Riyal',
        'RSD' => 'Serbian Dinar',
        'SCR' => 'Seychellois Rupee',
        'SGD' => 'Singapore Dollar',
        'SBD' => 'Solomon Islands Dollar',
        'SOS' => 'Somali Shilling',
        'ZAR' => 'South African Rand',
        'LKR' => 'Sri Lankan Rupee',
        'SEK' => 'Swedish Krona',
        'CHF' => 'Swiss Franc',
        'SRD' => 'Surinamese Dollar',
        'SYP' => 'Syrian Pound',
        'TWD' => 'Taiwanese New Dollar',
        'TZS' => 'Tanzanian Shilling',
        'THB' => 'Thai Baht',
        'TTD' => 'Trinidad and Tobago Dollar',
        'TND' => 'Tunisian Dinar',
        'TRY' => 'Turkish Lira',
        'TMT' => 'Turkmenistani Manat',
        'UGX' => 'Ugandan Shilling',
        'UAH' => 'Ukrainian Hryvnia',
        'AED' => 'United Arab Emirates Dirham',
        'GBP' => 'British Pound Sterling',
        'USD' => 'United States Dollar',
        'UYU' => 'Uruguayan Peso',
        'UZS' => 'Uzbekistani Som',
        'VUV' => 'Vanuatu Vatu',
        'VES' => 'Venezuelan Bolívar',
        'VND' => 'Vietnamese Dong',
        'XPF' => 'Wallis and Futuna CFP Franc',
        'YER' => 'Yemeni Rial',
        'ZMW' => 'Zambian Kwacha',
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


// Section Callbacks
function mpgc_api_settings_callback() {
    ?>
    <p><?php _e( 'Enter your API keys below. These keys are essential for the plugin to function correctly.', 'memberpress-geo-currency' ); ?></p>

    <div class="mpgc-accordion">
        <h3><?php _e( 'How to get an IPInfo API Key', 'memberpress-geo-currency' ); ?></h3>
        <div>
            <ol>
                <li><?php printf( __( 'Visit the IPInfo website: %s', 'memberpress-geo-currency' ), '<a href="https://ipinfo.io/signup" target="_blank">ipinfo.io</a>' ); ?></li>
                <li><?php _e( 'Sign up for a free or paid account.', 'memberpress-geo-currency' ); ?></li>
                <li><?php _e( 'Once logged in, go to your account dashboard.', 'memberpress-geo-currency' ); ?></li>
                <li><?php _e( 'You will find your API key listed under the "API Token" section.', 'memberpress-geo-currency' ); ?></li>
                <li><?php _e( 'Copy the API key and paste it into the "IPInfo API Key" field above.', 'memberpress-geo-currency' ); ?></li>
            </ol>
            <img src="<?php echo esc_url( MPGC_PLUGIN_URL . 'assets/images/ipinfo-api-key.png' ); ?>" alt="<?php esc_attr_e( 'Screenshot of IPInfo API Key location', 'memberpress-geo-currency' ); ?>">
        </div>

        <h3><?php _e( 'How to get an Open Exchange Rates API Key', 'memberpress-geo-currency' ); ?></h3>
        <div>
            <ol>
                <li><?php printf( __( 'Visit the Open Exchange Rates website: %s', 'memberpress-geo-currency' ), '<a href="https://openexchangerates.org/signup" target="_blank">openexchangerates.org</a>' ); ?></li>
                <li><?php _e( 'Sign up for a free or paid account.', 'memberpress-geo-currency' ); ?></li>
                <li><?php _e( 'After signing up, go to the "App IDs" tab in your dashboard.', 'memberpress-geo-currency' ); ?></li>
                <li><?php _e( 'Create a new App ID or copy an existing one.', 'memberpress-geo-currency' ); ?></li>
                <li><?php _e( 'Copy the App ID and paste it into the "Open Exchange Rates API Key" field above.', 'memberpress-geo-currency' ); ?></li>
            </ol>
            <img src="<?php echo esc_url( MPGC_PLUGIN_URL . 'assets/images/oer-api-key.png' ); ?>" alt="<?php esc_attr_e( 'Screenshot of Open Exchange Rates App ID location', 'memberpress-geo-currency' ); ?>">
        </div>
    </div>
    <?php
}