define([
    'jquery',
    'mage/translate',
    'otpFormsUtils',
    'jquery/ui',
    'jquery/validate'
], function($, $t, formsUtils){
    'use strict';
    return function() {
        $.validator.addMethod(
            'otp-6digits',
            function(value, element) {
                return formsUtils.isOtp6DigitsValid(value);
            },
            $t('OTP field must contain 6 digits.')
        );
    }
});
