jQuery(document).ready(function($) {

    // 1. Show/Hide API Key
    $('.mpgc-api-settings .form-table tr').each(function() {
        var $row = $(this);
        var $input = $row.find('input[type="text"]');

        if ($input.length) {
            var $toggleButton = $('<button type="button" class="button button-secondary">Show</button>');
            $toggleButton.insertAfter($input);

            $toggleButton.on('click', function() {
                var inputType = $input.attr('type');
                if (inputType === 'text') {
                    $input.attr('type', 'password');
                    $(this).text('Show');
                } else {
                    $input.attr('type', 'text');
                    $(this).text('Hide');
                }
            });
        }
    });

    // 2. Input Validation
    $('form').on('submit', function(e) {
        var ipInfoApiKey = $('#mpgc_ipinfo_api_key').val().trim();
        var oerApiKey = $('#mpgc_oer_api_key').val().trim();
        var cacheDuration = $('#mpgc_cache_duration').val().trim();
        var hasErrors = false;
        $('.mpgc-error').remove(); // Remove previous errors

        if (ipInfoApiKey === '') {
            $('#mpgc_ipinfo_api_key').after('<span class="mpgc-error" style="color:red; margin-left: 5px;">This field is required.</span>');
            hasErrors = true;
        }

        if (oerApiKey === '') {
            $('#mpgc_oer_api_key').after('<span class="mpgc-error" style="color:red; margin-left: 5px;">This field is required.</span>');
            hasErrors = true;
        }
        
        if (cacheDuration === '' || isNaN(cacheDuration) || parseInt(cacheDuration) < 1) {
            $('#mpgc_cache_duration').after('<span class="mpgc-error" style="color:red; margin-left: 5px;">Enter a valid number (minimum 1).</span>');
            hasErrors = true;
        }

        if (hasErrors) {
            e.preventDefault(); // Prevent form submission
            alert('Please correct the errors in the form.');
        }
    });

    // 3. Dynamic Currency Symbol
    var currencySymbol = $('#mpgc_default_currency_symbol');
    if (currencySymbol.length === 0) {
        // Add a span for the symbol if it doesn't exist
        $('#mpgc_default_currency').after('<span id="mpgc_default_currency_symbol"></span>');
        currencySymbol = $('#mpgc_default_currency_symbol');
    }

    function updateCurrencySymbol() {
        var selectedCurrency = $('#mpgc_default_currency').val();
        var symbol = getCurrencySymbol(selectedCurrency);
        currencySymbol.text(symbol);
    }

    $('#mpgc_default_currency').on('change', updateCurrencySymbol);

    // Initial symbol update on page load
    updateCurrencySymbol();


    function getCurrencySymbol(currencyCode) {
        switch (currencyCode) {
            case 'USD':
            case 'CAD':
            case 'AUD':
                return '$';
            case 'GBP':
                return '£';
            case 'EUR':
                return '€';
            case 'JPY':
                return '¥';
            default:
                return '';
        }
    }

    // 4. Cache Duration Helper
    var cacheHelper = $('#mpgc_cache_duration_helper');
    if(cacheHelper.length === 0) {
        $('#mpgc_cache_duration').after('<p id="mpgc_cache_duration_helper" class="description">Higher values reduce the number of API requests, but may display less up-to-date exchange rates.</p>');
    }
    
});