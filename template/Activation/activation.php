{% extends 'template.php' %}
{% block title %}Activate Account{% endblock %}
{% block js %}
{% if error %}
<script>
    Swal.fire({
        title: '{{error}}',
        text: "",
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
                data: $(this).serialize(),
                url: "{{url(getToken())}}"+"/{{username}}/{{userToken}}/resend",
                type: "POST",
                success: function(response){

                    new Promise(resolve => {
                        let obj = JSON.parse(response);

                        if (obj.status!==200) {
                            $.toast({
                                heading: 'Error',
                                text: obj.msg,
                                showHideTransition: 'fade',
                                icon: 'error',
                                position: 'top-center'
                            })
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