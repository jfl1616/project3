{% extends 'template.php' %}
{% block title %}Activate Account{% endblock %}
{% block js %}
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
{% if error %}
<script>
    Swal.fire({
        title: '{{error}}',
        html: $(".resendForm"),
        type: 'error',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Resend Activation Token',
        allowOutsideClick: false,
        allowEscapeKey: false,
        preConfirm:(result)=>{
            $('.swal2-modal').pleaseWait();

            $.ajax({
                data: $(".resendFormData").serialize(),
                url: "{{url(getToken())}}"+"/resend",
                type: "POST",
                success: function(response){
                    new Promise(resolve => {
                        let obj = JSON.parse(response);

                        if (obj.status!==200) {
                            //////////////////////////////////////
                            /// A dirty way to reload the Google
                            /// reCAPTCHA widget without reload
                            /// the page through the AJAX.
                            /// .swal2-content -> SweetAlert2 content
                            /// grepcaptcha.reset() -> Reset the widget
                            //////////////////////////////////////
                            $('#captcha').html('');
                            grecaptcha.reset();
                            let form = $(".resendForm")[0];
                            $('.swal2-content').html('');
                            $(".swal2-content")[0].append(form);
                            //////////////////////////////////////

                            $.toast({
                                heading: 'Error',
                                text: obj.msg,
                                showHideTransition: 'fade',
                                icon: 'error',
                                position: 'top-center'
                            });
                        } else {
                            sweet("success", "Check your email", obj.msg);
                        }

                        resolve(true);
                    }).catch(error=>{
                        swal.showValidationMessage(error);
                    })

                }
            }).then(function(){
                $(".swal2-modal").pleaseWait('stop');
            });
            return false;
        }
    })
</script>
{% endif %}
{% endblock %}

{% block content %}
<div style="display:none">
    <div class="resendForm">
        <div class="w-full max-w-xs mx-auto">
            <form method="POST" class="resendFormData bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="email" id="email" type="email" placeholder="Email">
                </div>
                <div class="mb-4">
                    <!-- Google reCAPTCHA box -->
                    <div id="captcha" class="g-recaptcha" data-theme="light" data-sitekey="6Lco-MEUAAAAAKXO_g5xpHJ_lrqadovDF7jB-Lqs" style="transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
                </div>
            </form>
        </div>
    </div>
</div>
{% endblock %}

