define([
    'Magento_Ui/js/form/element/abstract',
    'ko',
    'jquery',
    'mage/translate',
    'otpFormsUtils'
], function (Abstract, ko, $, $t, formsUtils) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'JaroslawZielinski_OTPComponent/form/element/otp',
            digits: 6,
            otpValues: [],
            isComplete: false,
            listens: {
                value: 'onValueChange'
            },
            otpContainer: '.otp-container',
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super();
            this.initializeOtpValues();
            this.setupObservables();
            return this;
        },

        /**
         * Initialize observables
         */
        initObservable: function () {
            return this._super()
                .observe(['otpValues', 'isComplete']);
        },

        /**
         * Setup additional observables and computed
         */
        setupObservables: function () {
            let self = this;

            // Computed observable for complete OTP value
            this.otpValue = ko.computed(function () {
                return self.otpValues().join('');
            });

            // Subscribe to OTP value changes
            this.otpValue.subscribe(function (newValue) {
                self.value(newValue);

                self.isComplete(newValue.length === parseInt(self.digits));

                if (self.isComplete()) {
                    self.onOtpComplete(newValue);
                }
            });
        },

        /**
         * Initialize OTP values array
         */
        initializeOtpValues: function () {
            let values = new Array(this.digits).fill('');

            // If there's an existing value, populate the array
            if (this.value() && this.value().length === this.digits) {
                values = this.value().split('');
            }

            this.otpValues(values);
        },

        /**
         * Handle input change for specific digit
         */
        onDigitChange: function (index, data, event) {
            let value = event.target.value;
            const currentValues = this.otpValues();

            // Only allow numeric input
            if (!/^\d*$/.test(value)) {
                event.target.value = currentValues[index] || '';
                return false;
            }

            // Take only the last entered digit
            if (value.length > 1) {
                value = value.slice(-1);
                event.target.value = value;
            }

            // Update the array
            currentValues[index] = value;
            this.otpValues(currentValues);

            // Move to next input if current is filled and not last
            if (value && index < this.digits - 1) {
                this.focusNextInput(index + 1);
            }

            return true;
        },

        /**
         * Handle keydown events
         */
        onKeyDown: function (index, data, event) {
            const currentValues = this.otpValues();

            switch (event.key) {
                case 'Backspace':
                    if (!currentValues[index] && index > 0) {
                        this.focusPreviousInput(index - 1);
                        currentValues[index] = '';
                    } else if (currentValues[index]) {
                        currentValues[index] = '';
                        this.otpValues(currentValues);
                    }
                    break;
                case 'ArrowLeft':
                    if (index > 0) {
                        this.focusPreviousInput(index - 1);
                    }
                    break;
                case 'ArrowRight':
                    if (index < this.digits - 1) {
                        this.focusNextInput(index + 1);
                    }
                    break;
                case 'Delete':
                    currentValues[index] = '';
                    this.otpValues(currentValues);
                    break;
            }

            return true;
        },

        /**
         * Handle paste event
         */
        onPaste: function (index, data, event) {
            event.preventDefault();

            const pasteData = (event.originalEvent.clipboardData || window.clipboardData).getData('text');
            const regex = new RegExp('^\\d{' + this.digits + '}$');

            if (regex.test(pasteData)) {
                const newValues = pasteData.split('');
                this.otpValues(newValues);
                this.focusInput(this.digits - 1);
            }

            return false;
        },

        /**
         * Handle focus event
         */
        onFocus: function (index, data, event) {
            event.target.select();
            return true;
        },

        /**
         * Focus specific input
         */
        focusInput: function (index) {
            const selector = '[data-otp-index="' + index + '"]';
            const element = $(this.otpContainer).find(selector);

            if (element.length) {
                setTimeout(function () {
                    element.focus();
                }, 10);
            }
        },

        /**
         * Focus next input
         */
        focusNextInput: function (index) {
            this.focusInput(index);
        },

        /**
         * Focus previous input
         */
        focusPreviousInput: function (index) {
            this.focusInput(index);
        },

        /**
         * Clear all OTP values
         */
        clearOtp: function () {
            const emptyValues = new Array(this.digits).fill('');
            this.otpValues(emptyValues);
            this.focusInput(0);
        },

        /**
         * Called when OTP is complete
         */
        onOtpComplete: function (otpValue) {
            // Trigger validation
            this.validate();

            // You can add additional logic here
            console.log('OTP Complete:', otpValue);
        },

        /**
         * Handle value change from parent
         */
        onValueChange: function (value) {
            if (value && value.length === this.digits && value !== this.otpValue()) {
                this.otpValues(value.split(''));
            } else if (!value) {
                this.clearOtp();
            }
        },

        /**
         * Set container reference after render
         */
        afterRender: function (elements) {
            // Focus first input after render
            setTimeout(() => {
                this.focusInput(0);
            }, 100);
        },

        /**
         * Get CSS classes for input
         */
        getInputClasses: function (index) {
            let classes = 'admin__control-text otp-field otp-field-' + index;
            const currentValues = this.otpValues();

            if (currentValues[index]) {
                classes += ' filled';
            }

            return classes;
        },

        /**
         * Get CSS classes for CheckMark
         */
        getCheckMarkClasses: function () {
            const classes = 'checkmark';
            if (this.isComplete()) {
                return classes;
            } else {
                return classes + ' hide-me';
            }
        },

        /**
         * Validate OTP
         */
        validate: function () {
            this._super();

            let value = this.value(),
                message = '',
                isValid = true;

            const optResult = this.isComplete() && formsUtils.isOtp6DigitsValid(value);
            if (!optResult) {
                isValid = false;
                message = $t('OTP field must contain 6 digits.')
            }

            this.error(message);
            this.error.valueHasMutated();
            this.bubble('error', message);

            //TODO: Implement proper result propagation for form
            if (this.source && !isValid) {
                this.source.set('params.invalid', true);
            }

            return {
                valid: isValid,
                target: this
            };
        },
    });
});
