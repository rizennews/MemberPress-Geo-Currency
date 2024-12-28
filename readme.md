# MemberPress Geo Currency

Seamlessly display prices in your users' local currency within MemberPress, enhancing their experience and boosting conversions.

## Description

MemberPress Geo Currency is a powerful WordPress plugin designed to automatically adjust the displayed currency for your MemberPress products and memberships based on the visitor's geographical location. By providing a localized experience, you can significantly improve user engagement and reduce cart abandonment among your international customer base.

This plugin leverages the IPInfo API for accurate geolocation and the Open Exchange Rates API for real-time currency exchange rates. It integrates smoothly with MemberPress, dynamically converting prices on your membership plans and product pages without requiring any manual intervention.

## Features

*   **Automatic Currency Conversion:** Effortlessly converts prices to the user's local currency based on their IP address.
*   **Accurate Geolocation:** Utilizes the IPInfo API to reliably determine the user's country.
*   **Real-time Exchange Rates:** Fetches up-to-date exchange rates from the Open Exchange Rates API, ensuring accurate and current pricing.
*   **Intelligent Caching:** Caches both geolocation data and exchange rates to minimize API requests, reduce server load, and improve website performance.
*   **Seamless MemberPress Integration:** Hooks directly into MemberPress's price display filters for a smooth and consistent user experience.
*   **Intuitive Admin Settings:** Offers a user-friendly settings page within the WordPress dashboard for easy configuration:
    *   IPInfo API Key
    *   Open Exchange Rates API Key
    *   Default Currency (used as a fallback when geolocation is unavailable or fails)
    *   Customizable Cache Duration
*   **Multilingual Ready:** Includes a `.pot` file for easy translation into multiple languages, catering to a global audience.
*   **Developer-Friendly Architecture:** Provides hooks and filters for advanced customization and extension, allowing developers to tailor the plugin to their specific needs.

## Requirements

*   WordPress 5.0 or higher
*   PHP 7.4 or higher
*   MemberPress plugin (latest version recommended)
*   IPInfo API Key (free or paid plan)
*   Open Exchange Rates API Key (free or paid plan)
*   `intl` PHP extension (required for proper currency formatting)

## Installation

1.  **Download the Plugin:** Obtain the latest version of the plugin from the [Releases](https://github.com/rizennews/memberpress-geo-currency/releases) page.
2.  **Upload and Activate:** In your WordPress admin panel, navigate to Plugins -> Add New -> Upload Plugin. Upload the downloaded zip file and activate the plugin.
3.  **Configure Plugin Settings:** Go to the "Geo Currency" settings page in your WordPress admin. Enter your IPInfo API key, Open Exchange Rates API key, default currency, and desired cache duration.

## Usage

Once installed and configured, the plugin will automatically convert prices displayed by MemberPress based on the user's location. No further action is required on your part. The plugin works silently in the background to provide a seamless experience for your users.

## Frequently Asked Questions (FAQ)

**Q: How accurate is the geolocation detection?**

**A:** The accuracy of geolocation detection is dependent on the IPInfo API and the user's IP address. Generally, it provides accurate results at the country level.

**Q: What happens if the geolocation lookup fails?**

**A:** In cases where geolocation fails, the plugin will gracefully fall back to the default currency you have configured in the plugin settings.

**Q: How often are exchange rates updated?**

**A:** Exchange rates are fetched and cached based on the cache duration you specify in the plugin settings. The default setting is 24 hours, but you can adjust this to suit your needs.

**Q: Will this plugin work with all MemberPress pricing page styles?**

**A:** The plugin is designed to work with most standard MemberPress pricing page styles, as it utilizes the core MemberPress price display filters. However, if you are using a heavily customized pricing page, you may need to make minor adjustments to ensure compatibility.

**Q: Which currencies are supported by the plugin?**

**A:** The plugin supports a wide range of global currencies, dynamically fetched from the Open Exchange Rates API. This ensures that you can cater to a diverse international audience.

## Support

If you encounter any issues or have questions about the plugin, please feel free to open an issue on the [GitHub Issues](https://github.com/rizennews/memberpress-geo-currency/issues) page.

## Contributing

We welcome contributions from the community! If you are interested in contributing to the development of this plugin, please fork the repository and submit a pull request with your proposed changes.

## License

This plugin is licensed under the GPLv2 or later.

## Credits

This plugin was developed by [Padmore Aning].

It utilizes the following third-party services:

*   [IPInfo](https://ipinfo.io/) for geolocation services.
*   [Open Exchange Rates](https://openexchangerates.org/) for real-time currency exchange rates.

## Changelog

**1.0.0** (Initial Release)

*   Initial release of the MemberPress Geo Currency plugin.
*   Automatic currency conversion based on user geolocation.
*   Admin settings for configuring API keys, default currency, and cache duration.
*   Seamless integration with MemberPress.
*   Caching mechanisms for geolocation and exchange rates.
*   Multilingual support for a global audience.
