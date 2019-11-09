<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Title Page-->
    <title>{% block title %} foo {% endblock %}</title>

    <!-- Icons font CSS-->
    <link href="{{asset('css/material-design-iconic-font.min.css')}}" rel="stylesheet" media="all">
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>

    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="{{asset('css/select2.min.css')}}" rel="stylesheet" media="all">
    <link href="{{asset('css/tailwind.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/jquery.toast.css')}}" rel="stylesheet">

    <!-- Custom CSS -->
    {% block css %} {% endblock %}
</head>

<body>
{% block content %} {% endblock %}
</body>

<!-- Jquery JS-->
<script src="{{asset('js/jquery.min.js')}}"></script>
<!-- Vendor JS-->
<script src="{{asset('js/select2.min.js')}}"></script>
<script src="{{asset('js/pleaseWait.js')}}"></script>
<script src="{{asset('js/jquery.toast.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script>
    function sweet(type, title, message){
        Swal.fire({
            type: type,
            title: title,
            text: message,
        })
    }
    /* Return value of any parameters variable
    * Credit: http://www.jquerybyexample.net/2012/06/get-url-parameters-using-jquery.html
    * */
    function getURLParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam) {
                return sParameterName[1];
            }
        }
    }
</script>
<!-- Custom JS -->
{% block js %} {% endblock %}

</body>
</html>
