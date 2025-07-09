define([
    'jquery'
], function($) {
    'use strict';

    return {
        showLoader: function(formId) {
            $('#' + formId + '-loader').removeAttr('hidden');
            $('#' + formId + '-submit').attr('hidden', true);
        },
        hideLoader: function(formId) {
            $('#' + formId + '-loader').attr('hidden', true);
            $('#' + formId + '-submit').removeAttr('hidden');
        },
        disableSubmitButton: function(formId, isDisabled) {
            $('form#' + formId + ' :button[type="submit"]').prop('disabled', isDisabled);
        },
        resetFormById: function(formId) {
            const reCaptchaIds = window.reCaptchaIds || [];
            if (undefined !== reCaptchaIds[formId]) {
                grecaptcha.reset(reCaptchaIds[formId]);
                this.disableSubmitButton(formId, false);
            }
            //reset form
            let forms = $('#' + formId);
            let form = forms[0];
            try {
                $(form).validation('clearError');
            } catch (e) {
            }
            $(form).trigger('reset');
            //hide success message as well
            $('#message-' + formId).hide();
            $('#parent-' + formId).show();
            //ajax Loader Off
            this.hideLoader(formId);
        }
    };
});
