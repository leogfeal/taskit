$(document).ready(function() {
    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    });
    $.validator.addMethod('nameImage', function(value, element) {
        return this.optional(element) || /^[a-zA-Z\d]{1}.*$/.test(value);
    });
    $.validator.addMethod("specialCharacters", function(value, element) {
        var validate = true;
        var characters = '\/:*?<>|';
        for(var i=0; i < characters.length; i++ ) {
           if(value.indexOf(characters.charAt(i)) !== -1) {
               validate = false;
               break;
           }
        }
        return validate;
    }, "Letters, numbers, and underscores only please");

    $(document).on('ready', function () {
        addRulesCountry();
    });

    function addRulesCountry(){
        if($('#appbundle_user_admin_country').val() != ''){
            $('#appbundle_user_admin_country').rules('add', {
                required: true,
                messages:{
                    required: Translator.trans('field.required', {}, 'validators')
                }
            });
        }
    };

    var validator = $('form').validate({        
        errorPlacement: function(error, element) {
            if ($(element).attr('id') === 'appbundle_user_admin_profile_image') {
                $(element).parent().parent().parent().find('i.e-validate').remove();
                $(element).parent().parent().next('i').remove();
                $(element).parent().parent().parent().find('label:first').before('<i class="e-validate text-danger fa fa-user-times fa-fw hidden-sm hidden-xs" title="' + $(error).html() + '"></i> ');
                $(element).parent().parent().after('<i class="e-validate text-danger hidden-md hidden-lg">' + $(error).html() + '</i>');
            } else {
                $(element).parent().parent().find('i.e-validate').remove();
                $(element).next('i').remove();
                $(element).parent().addClass('has-error');
                $(element).parent().parent().find('label:first').before('<i class="e-validate text-danger fa fa-user-times fa-fw hidden-sm hidden-xs" title="' + $(error).html() + '"></i> ');
                $(element).after('<i class="e-validate text-danger hidden-md hidden-lg">' + $(error).html() + '</i>');
            }
            $('i.hidden-sm').tooltip({'placement': 'bottom'});
        },
        errorElement: 'i',
        errorClass: 'e-validate',
        ignore: "",
        rules: {
            'appbundle_user_admin[name]': {
                required: true,
                nameImage: true
            },
            'appbundle_user_admin[last_names]': {
                required: true,
                nameImage: true
            },
            'appbundle_user_admin[username]': {
                required: true,
                //specialCharacters: true,
            },
            'appbundle_user_admin[email]': {
                required: true,
                email: true
            },
            'appbundle_user_admin[password][first]': {
                minlength: 6,
            },
            'appbundle_user_admin[password][second]': {
                minlength: 6,
                equalTo: '#appbundle_user_admin_password_first'
            },
            'appbundle_user_admin[profile_image]': {
                required: false,
                accept: 'image/jpeg | image/png | image/gif',
                filesize: 2048 * 1024
            }
        },
        messages: {
            'appbundle_user_admin[name]':  {
                required : Translator.trans('field.required', {}, 'validators'),
                nameImage : Translator.trans('user.name.image', {}, 'validators'),
            },
            'appbundle_user_admin[last_names]': {
                required : Translator.trans('field.required', {}, 'validators'),
                nameImage : Translator.trans('user.name.image', {}, 'validators'),
            },
            'appbundle_user_admin[username]': {
                required : Translator.trans('field.required', {}, 'validators'),
                //specialCharacters : Translator.trans('alphanumeric.error', {}, 'validators'),
            },
            'appbundle_user_admin[email]': {
                required: Translator.trans('field.required', {}, 'validators'),
                email: Translator.trans('email.invalid', {}, 'validators')
            },
            'appbundle_user_admin[password][first]': {
                minlength: Translator.trans('min.six', {}, 'validators')
            },
            'appbundle_user_admin[password][second]': {
                minlength: Translator.trans('min.six', {}, 'validators'),
                equalTo: Translator.trans('pwd.same', {}, 'validators')
            },
            'appbundle_user_admin[profile_image]': {
                accept: Translator.trans('image.error.type', {}, 'validators'),
                filesize: Translator.trans('error.size', {'size': '2MB'}, 'validators'),
            }
        },
        unhighlight: function(element) {
            if ($(element).attr('id') === 'appbundle_user_admin_profile_image') {
                $(element).parent().parent().parent().find('i.e-validate').remove();
                $(element).parent().parent().next('i').remove();
            } else {
                $(element).parent().parent().find('i.e-validate').remove();
                $(element).next('i').remove();
                $(element).parent().removeClass('has-error');
            }
        }
    });
});