
define([
    'jquery',
    'underscore',
    'mage/translate',
    'otpFormsUtils'
], function($, _, $t, formsUtils) {
    'use strict';

    $.widget('otpcomponent.otpWidget', {
        defaults: {
            parts: {
                digit1: '',
                digit2: '',
                digit3: '',
                digit4: '',
                digit5: '',
                digit6: ''
            }
        },
        htmlID: '',
        _value: null,
        /**
         * Merge global options with options passed to widget invoke
         * @protected
         */
        _create: function () {
            this.options = $.extend(
                this.defaults,
                this.options
            );
            this._initElement(this.element);
            this._initOtp();
        },
        /**
         * Init Element
         * @protected
         * @param {Object} element
         */
        _initElement: function (element) {
            //add html ID
            let htmlId = 'otp_';
            htmlId += Date.now();
            htmlId += '_' + Math.floor(Math.random() * 1000);
            $(element).attr('id', htmlId);
            this.htmlID = htmlId;
        },
        _initOtp: function () {
            this.updateState(this.options.initialValue);
            this.updateUi();
            this.bindElements();
            this.verifyOtp();
            if (!this.isComplete()) {
                $('#' + this.htmlID + ' .otp-field-1').focus();
            } else {
                $('#' + this.htmlID + ' .otp-field-6').focus();
            }
        },
        bindElements: function() {
            let self = this;
            const htmlID = '#' + self.htmlID + ' .otp-field';
            $(htmlID).on('input', function() {
                const currentInput = $(this);
                const value = currentInput.val();
                if (!/^\d$/.test(value)) {
                    currentInput.val('');
                    return;
                }
                if (1 === value.length) {
                    const nextInput = currentInput.next(htmlID);
                    if (nextInput.length) {
                        nextInput.focus();
                    }
                }
                self.verifyOtp();
            });
            $(htmlID).on('keydown', function(event) {
                const currentInput = $(this);
                switch (event.key) {
                    case 'Backspace':
                        if ('' === currentInput.val()) {
                            const prevInput = currentInput.prev(htmlID);
                            if (prevInput.length) {
                                prevInput.focus();
                                prevInput.val('');
                            }
                        } else {
                            currentInput.val('');
                        }
                        self.verifyOtp();
                        break;
                    case 'ArrowLeft':
                        const prevInput = currentInput.prev(htmlID);
                        if (prevInput.length) {
                            prevInput.focus();
                        }
                        break;
                    case 'ArrowRight':
                        const nextInput = currentInput.next(htmlID);
                        if (nextInput.length) {
                            nextInput.focus();
                        }
                        break;
                    case 'Delete':
                        currentInput.val('');
                        break;
                }
            });
            $(htmlID).on('paste', function(event) {
                event.preventDefault();
                const pastedData = event.originalEvent.clipboardData.getData('text');
                const digits = pastedData.replace(/\D/g, '').substring(0, 6);
                $(htmlID).val('');
                for (let i = 0; i < digits.length; i++) {
                    $(`#` + self.htmlID + ` .otp-field-${i + 1}`).val(digits[i]);
                }
                if (digits.length < 6) {
                    $(`#` + self.htmlID + ` .otp-field-${digits.length + 1}`).focus();
                } else {
                    $('#' + self.htmlID + ' .otp-field-6').focus();
                }
                self.verifyOtp();
            });
            $(htmlID).on('keypress', function(event) {
                if (!/\d/.test(String.fromCharCode(event.which))) {
                    event.preventDefault();
                }
            });
        },
        updateState: function (value) {
            try {
                const parts = value.split('');
                this.options.parts.digit1 = parts[0];
                this.options.parts.digit2 = parts[1];
                this.options.parts.digit3 = parts[2];
                this.options.parts.digit4 = parts[3];
                this.options.parts.digit5 = parts[4];
                this.options.parts.digit6 = parts[5];
            } catch (event) {}
        },
        updateValue: function() {
            return this.options.parts.digit1 +
                this.options.parts.digit2 +
                this.options.parts.digit3 +
                this.options.parts.digit4 +
                this.options.parts.digit5 +
                this.options.parts.digit6;
        },
        value: function (val) {
            if (undefined !== val) {
                this._value = val;
            }
            return !formsUtils.isEmpty(this._value) ? this._value : '';
        },
        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            $('#' + this.htmlID + ' input[name="' + this.options.name + '"]').attr('value', this._value);
        },
        updateIndicator: function () {
            $('#' + this.htmlID + ' .otp-field-1').val(this.options.parts.digit1);
            $('#' + this.htmlID + ' .otp-field-2').val(this.options.parts.digit2);
            $('#' + this.htmlID + ' .otp-field-3').val(this.options.parts.digit3);
            $('#' + this.htmlID + ' .otp-field-4').val(this.options.parts.digit4);
            $('#' + this.htmlID + ' .otp-field-5').val(this.options.parts.digit5);
            $('#' + this.htmlID + ' .otp-field-6').val(this.options.parts.digit6);
        },
        updateUi: function () {
            try {
                this.value(this.updateValue());
                this.onUpdate();
                this.updateIndicator();
            } catch (event) {}
        },
        fetchValue: function () {
            this.options.parts.digit1 = $('#' + this.htmlID + ' .otp-field-1').val();
            this.options.parts.digit2 = $('#' + this.htmlID + ' .otp-field-2').val();
            this.options.parts.digit3 = $('#' + this.htmlID + ' .otp-field-3').val();
            this.options.parts.digit4 = $('#' + this.htmlID + ' .otp-field-4').val();
            this.options.parts.digit5 = $('#' + this.htmlID + ' .otp-field-5').val();
            this.options.parts.digit6 = $('#' + this.htmlID + ' .otp-field-6').val();
        },
        isComplete: function () {
            return $('#' + this.htmlID + ' .otp-field').toArray().every(input => 1 === $(input).val().length);
        },
        verifyOtp: function () {
            this.fetchValue();
            this.value(this.updateValue());
            this.onUpdate();
            if (this.isComplete() && formsUtils.isOtp6DigitsValid(this._value)) {
                $('#' + this.htmlID + ' .checkmark').removeClass('hide-me');
            } else {
                $('#' + this.htmlID + ' .checkmark').removeClass('hide-me').addClass('hide-me');
            }
        }
    });

    return $.otpcomponent.otpWidget;
});
