var user = function () {
    return {
        initUsers: function(){
            validateFormUser();
        },
        initProfile: function () {
            fnDisableInput();
            eventEnableInput();
            fnSubmitForm();
        },
        onValidateProfile: function (){
            $('form').validate({
                errorPlacement: function(error, element) {
                    $(element).parent().parent().find('i.e-validate').remove();
                    $(element).next('i').remove();
                    $(element).parent().addClass('has-error');
                    $(element).parent().parent().find('label:first').before('<i class="e-validate text-danger fa fa-user-times fa-fw hidden-sm hidden-xs" title="' + $(error).html() + '"></i> ');
                    $(element).after('<i class="e-validate text-danger hidden-md hidden-lg">' + $(error).html() + '</i>');
                },
                errorElement: 'i',
                errorClass: 'e-validate',
                rules: {},
                messages: {},
                unhighlight: function(element) {
                    $(element).parent().parent().find('i.e-validate').remove();
                    $(element).next('i').remove();
                    $(element).parent().removeClass('has-error');
                }
            });
        },
        validateForgotPasswordStep1: function () {
            eventSearchForgotPassword();
            $('form').validate({
                // Rules for form validation
                errorElement: "em",
                errorClass: 'state-error',
                validClass: 'state-success',
                errorLabelContainer: '#',
                rules: {
                    'email': {
                        required: true,
                        email: true,
                        remote: {
                            url: Routing.generate('check_email_forget_password'),
                            type: 'get',
                            data: {
                                key: $('#email').attr('name')
                            },
                            beforeSend: function () {
                                $('#email').addClass('loading-input');
                            },
                            complete: function () {
                                $('#email').removeClass('loading-input');
                            }
                        }
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parent().addClass(errorClass).removeClass(validClass);
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parent().removeClass(errorClass).addClass(validClass);//
                },
                messages: {
                    'email': {
                        required: 'This field is required',
                        email: 'Invalid email',
                        remote: 'Email no found'
                    }
                },
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
        },
        validateForgotPasswordStep2: function () {
            $.validator.addMethod('exactlength', function (value, element, param) {
                return this.optional(element) || value.length == param;
            });
            $('#fp-step2').validate({
                // Rules for form validation
                errorElement: "em",
                errorClass: 'state-error',
                validClass: 'state-success',
                errorLabelContainer: '#',
                rules: {
                    'code': {
                        required: true,
                        exactlength: 6,
                        remote: {
                            url: Routing.generate('check_code_forget_pswd'),
                            type: 'get',
                            data: {
                                key: $('#code').attr('name'),
                                email: $('#email').val()
                            },
                            beforeSend: function () {
                                $('#code').addClass('loading-input');
                            },
                            complete: function () {
                                $('#code').removeClass('loading-input');
                            }
                        }
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parent().addClass(errorClass).removeClass(validClass);
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parent().removeClass(errorClass).addClass(validClass);//
                },
                messages: {
                    'code': {
                        required: 'Field required',
                        exactlength: 'The code must be of 6 characters',
                        remote: 'The code is not correct'
                    }
                },
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
        },
        validateForgotPasswordStep3: function () {
            $('#fp-step3').validate({
                // Rules for form validation
                errorElement: "em",
                errorClass: 'state-error',
                validClass: 'state-success',
                errorLabelContainer: '#',
                rules: {
                    'new_password[password][first]': {
                        required: true,
                        minlength: 6
                    },
                    'new_password[password][second]': {
                        required: true,
                        minlength: 6,
                        equalTo: '#new_password_password_first'
                    }
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parent().addClass(errorClass).removeClass(validClass);
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parent().removeClass(errorClass).addClass(validClass);//
                },
                messages: {
                    'new_password[password][first]': {
                        required: 'Field required',
                        minlength: 'min 6 characters'
                    },
                    'new_password[password][second]': {
                        required: 'Field required',
                        minlength: 'min 6 characters',
                        equalTo: 'password must match'
                    }
                },
                errorPlacement: function (error, element) {
                    error.insertAfter(element.parent());
                }
            });
        },
        settings: {
            action:true
        }
    };

    function validateFormUser(){
        taskit.validateSpecialCharacters1();
        $('form').validate({
            errorPlacement: function(error, element) {
                $(element).parent().parent().find('i.e-validate').remove();
                $(element).next('i').remove();
                $(element).parent().addClass('has-error');
                $(element).parent().parent().find('label:first').before('<i class="e-validate text-danger fa fa-user-times fa-fw hidden-sm hidden-xs" title="' + $(error).html() + '"></i> ');
                $(element).after('<i class="e-validate text-danger hidden-md hidden-lg">' + $(error).html() + '</i>');
            },
            errorElement: 'i',
            errorClass: 'e-validate',
            rules: {
                'appbundle_user[name]': {
                    required: true,
                    maxlength:40
                },
                'appbundle_user[username]': {
                    required: true,
                    maxlength:40,
                    specialCharacters1: true
                },
                'appbundle_user[password][first]': {
                    required: user.settings.action,
                    minlength: 6
                },
                'appbundle_user[password][second]': {
                    required: user.settings.action,
                    minlength: 6,
                    equalTo: '#appbundle_user_password_first'
                },
                'appbundle_user[email]': {
                    required: true,
                    email: true
                },
                'appbundle_user[phone]':{
                    digits: true
                }
            },
            messages: {
                'appbundle_user[name]': {
                    required: Translator.trans('field.required', {}, 'validators'),
                    maxlength: Translator.trans('error.max.length', {'length':'40'}, 'validators')
                },
                'appbundle_user[username]': {
                    required: Translator.trans('field.required', {}, 'validators'),
                    maxlength: Translator.trans('error.max.length', {'length':'40'}, 'validators'),
                    specialCharacters1: Translator.trans('error.specialCharacters', {'length':'40'}, 'validators')
                },
                'appbundle_user_admin[password][first]': {
                    required: Translator.trans('field.required', {}, 'validators'),
                    minlength: Translator.trans('min.six', {}, 'validators')
                },
                'appbundle_user[password][second]': {
                    required: Translator.trans('field.required', {}, 'validators'),
                    minlength: Translator.trans('min.six', {}, 'validators'),
                    equalTo: Translator.trans('pwd.same', {}, 'validators')
                },
                'appbundle_user[email]': {
                    required: Translator.trans('field.required', {}, 'validators'),
                    email: Translator.trans('email.invalid', {}, 'validators')
                },
                'appbundle_user[phone]':{
                    digits: Translator.trans('field.digits', {}, 'validators')
                }
            },
            unhighlight: function(element) {
                $(element).parent().parent().find('i.e-validate').remove();
                $(element).next('i').remove();
                $(element).parent().removeClass('has-error');
            }
        });
    }

    function eventEnableInput(){
        $(document).on('click', 'button.btn-enable', function (e) {
            var input = $(this).attr('id').split('-');
            if(input[1] == 'appbundle_user_current_password'){
                $('#'+input[1]).prop('disabled', false);
                $('#appbundle_user_password_first').prop('disabled', false);
                $('#appbundle_user_password_second').prop('disabled', false);
                $('#checkPassword').val('true');
            }
            else
                $('#'+input[1]).prop('disabled', false);
            $('#btn-submit-form').prop('disabled', false);
        });
    }

    function fnDisableInput(){
        $("input").attr('disabled','disabled');
    }

    function fnEnableInput(){
        $("input").removeAttr('disabled');
    }

    function fnGetValueInputEnable(){
        var dataChange = $('#changeData').val();
        inputs = $("input:enabled" );
        inputs.each(function(){
            var item = $(this);
            if(item.attr('name') != 'appbundle_user[password][first]' && item.attr('name') != 'appbundle_user[password][second]' && item.attr('name') != 'appbundle_user[currect_password]'
                && item.attr('name') != 'btn-submit-form'){
                var temp = '#'+item.attr('id')+':'+item.val();
                dataChange += (dataChange == '')?temp:','+temp;
            }
        });
        $('#changeData').val(dataChange);
    }

    function fnAddRulesValidate(){
        inputs = $("input:enabled" );
        inputs.each(function(){
            var item = $(this);
            if(item.attr('name') == 'appbundle_user[password][first]'){
                $(this).rules("remove", "minlength required");
                $(this).rules('add', {
                    minlength: 6,
                    required: true,
                    messages:{
                        minlength: Translator.trans('min.six', {}, 'validators'),
                        required : Translator.trans('field.required', {}, 'validators')
                    }
                });
            }
            else if(item.attr('name') == 'appbundle_user[password][second]'){
                $(this).rules("remove", "minlength required equalTo");
                $(this).rules('add', {
                    required: true,
                    minlength: 6,
                    equalTo: '#appbundle_user_password_first',
                    messages:{
                        minlength: Translator.trans('min.six', {}, 'validators'),
                        required : Translator.trans('field.required', {}, 'validators'),
                        equalTo: Translator.trans('pwd.same', {}, 'validators')
                    }
                });
            }
            else if(item.attr('name') == 'appbundle_user[email]'){
                $(this).rules("remove", "required email");
                $(this).rules('add', {
                    required: true,
                    email: true,
                    messages:{
                        required : Translator.trans('field.required', {}, 'validators'),
                        email: Translator.trans('email.invalid', {}, 'validators')
                    }
                });
            }
            else{
                $(this).rules("remove", "required");
                $(this).rules('add', {
                    required: true,
                    messages:{
                        required : Translator.trans('field.required', {}, 'validators')
                    }
                });
            }
        });
    }

    function fnSubmitForm(){
        $(document).on('click', '#btn-submit-form', function (e) {
            fnAddRulesValidate();
            if($('form').valid()){
                fnGetValueInputEnable();
                fnEnableInput();
                $('form').submit();
            }
        });
    }

    function eventSearchForgotPassword(){
        $(document).on('click', '#btn-search-forgot-password', function (e) {
            $('#email').addClass('loading-input');
        });
    }

}();

