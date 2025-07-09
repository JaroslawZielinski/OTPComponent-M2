define([
    'jquery'
], function($) {
    'use strict';

    return {
        /**
         * Is Empty
         * @see https://stackoverflow.com/questions/154059/how-do-i-check-for-an-empty-undefined-null-string-in-javascript#answer-3261380
         * @param str
         * @returns {boolean}
         */
        isEmpty: function (str) {
            return (!str || str.length === 0 );
        },
        /**
         * Validation test - Is a number (like Magento 'validate-digits') and 6 digits
         * @param value
         * @returns {boolean}
         */
        isOtp6DigitsValid: function (value) {
            return !/\D/.test(value) && 6 === value.length;
        },
        /**
         * @see https://magento.stackexchange.com/questions/248677/magento-2-how-to-call-text-x-magento-init-on-ajax-success#answer-248703
         * @param htmlId - id of an element to reinit
         */
        ajaxMagentoReinit: function (htmlId) {
            try {
                $(htmlId).trigger('contentUpdated');
                if ($.fn.applyBindings !== undefined) {
                    $(htmlId).applyBindings();
                }
            } catch (e) { }
        }
    };
});
