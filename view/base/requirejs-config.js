var config = {
    map: {
        '*': {
            otpValidateMethod: 'JaroslawZielinski_OTPComponent/js/otpValidateField',
            otpWidget: 'JaroslawZielinski_OTPComponent/js/otpWidget'
        }
    },
    paths: {
        otpForms: 'JaroslawZielinski_OTPComponent/js/forms',
        otpFormsUtils: 'JaroslawZielinski_OTPComponent/js/forms/utils'
    },
    shim: {
        otpWidget: {
            deps: ['jquery']
        },
        otpForms: {
            deps: ['jquery']
        },
        otpFormsUtils: {
            deps: ['jquery']
        }
    }
};
