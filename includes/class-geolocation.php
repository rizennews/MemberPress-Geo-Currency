<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants for option names (could also be in your main plugin file)
define( 'MPGC_IPINFO_API_KEY_OPTION', 'mpgc_ipinfo_api_key' );
define( 'MPGC_CACHE_DURATION_OPTION', 'mpgc_cache_duration' );
define( 'MPGC_DEFAULT_COUNTRY_OPTION', 'mpgc_default_country' ); // Option for default country

class MPGC_Geolocation {
    private $ipinfo_api_key;
    private $base_url = 'https://ipinfo.io/';
    private $cache_key_prefix = 'mpgc_user_location_';
    private $cache_duration;
    private $default_country;

    public function __construct() {
        $this->ipinfo_api_key = get_option( MPGC_IPINFO_API_KEY_OPTION );
        $this->cache_duration = get_option( MPGC_CACHE_DURATION_OPTION, 24 ); // Default 24 hours
        $this->default_country = get_option( MPGC_DEFAULT_COUNTRY_OPTION, 'US' ); // Default country US
    }

    /**
     * Get user's IP address.
     */
    public function get_user_ip() {
        $ip = '';

        // Consider proxies and load balancers
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ips[0]);
        } elseif( !empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Validate IP (basic check for public IP) - you might want a more robust check
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
            $ip = ''; // Reset to empty if invalid
        }

        return apply_filters( 'mpgc_user_ip', $ip );
    }

    /**
     * Get user's country code from IPInfo API or cache.
     */
    public function get_user_country() {
        $ip_address = $this->get_user_ip();

        if ( empty( $ip_address ) || empty( $this->ipinfo_api_key ) ) {
            return $this->default_country; // If no IP or API key, return default country
        }

        $cache_key = $this->cache_key_prefix . $ip_address;
        $country_code = get_transient( $cache_key );

        if ( false === $country_code ) {
            // Use IPInfo API
            $url = $this->base_url . $ip_address . '?token=' . $this->ipinfo_api_key;
            $response = wp_remote_get( esc_url_raw( $url ) );

            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                return $this->default_country; // Handle error, return default country
            }

            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );

            if ( isset( $data['country'] ) ) {
                $country_code = sanitize_text_field( $data['country'] );
                set_transient( $cache_key, $country_code, absint( $this->cache_duration ) * HOUR_IN_SECONDS );
            } else {
                return $this->default_country; // Handle error, return default country
            }
        }

        return $country_code;
    }
}