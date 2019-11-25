{% extends 'template.php' %}

{% block title %}Lobby{% endblock %}

{% block css %}
<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background: #27ae60;
        font-family: "proxima-nova", "Source Sans Pro", sans-serif;
        font-size: 1em;
        letter-spacing: 0.1px;
        color: #32465a;
        text-rendering: optimizeLegibility;
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.004);
        -webkit-font-smoothing: antialiased;
    }

    #frame {
        width: 95%;
        min-width: 360px;
        max-width: 1000px;
        height: 92vh;
        min-height: 300px;
        max-height: 720px;
        background: #E6EAEA;
    }
    @media screen and (max-width: 360px) {
        #frame {
            width: 100%;
            height: 100vh;
        }
    }
    #frame #sidepanel {
        float: left;
        min-width: 280px;
        max-width: 340px;
        width: 40%;
        height: 100%;
        background: #2c3e50;
        color: #f5f5f5;
        overflow: hidden;
        position: relative;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel {
            width: 58px;
            min-width: 58px;
        }
    }
    #frame #sidepanel #profile {
        width: 80%;
        margin: 25px auto;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile {
            width: 100%;
            margin: 0 auto;
            padding: 5px 0 0 0;
            background: #32465a;
        }
    }
    #frame #sidepanel #profile.expanded .wrap {
        height: 210px;
        line-height: initial;
    }
    #frame #sidepanel #profile.expanded .wrap p {
        margin-top: 20px;
    }
    #frame #sidepanel #profile.expanded .wrap i.expand-button {
        -moz-transform: scaleY(-1);
        -o-transform: scaleY(-1);
        -webkit-transform: scaleY(-1);
        transform: scaleY(-1);
        filter: FlipH;
        -ms-filter: "FlipH";
    }
    #frame #sidepanel #profile .wrap {
        height: 60px;
        line-height: 60px;
        overflow: hidden;
        -moz-transition: 0.3s height ease;
        -o-transition: 0.3s height ease;
        -webkit-transition: 0.3s height ease;
        transition: 0.3s height ease;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap {
            height: 55px;
        }
    }
    #frame #sidepanel #profile .wrap img, #profile .wrap svg {
        width: 50px;
        border-radius: 50%;
        padding: 3px;
        border: 2px solid #e74c3c;
        height: auto;
        float: left;
        cursor: pointer;
        -moz-transition: 0.3s border ease;
        -o-transition: 0.3s border ease;
        -webkit-transition: 0.3s border ease;
        transition: 0.3s border ease;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap img {
            width: 40px;
            margin-left: 4px;
        }
    }
    #frame #sidepanel #profile .wrap img.online, #profile .wrap svg.online {
        border: 2px solid #2ecc71;
    }
    #frame #sidepanel #profile .wrap img.away, #profile .wrap svg.away {
        border: 2px solid #f1c40f;
    }
    #frame #sidepanel #profile .wrap img.busy, #profile .wrap svg.busy {
        border: 2px solid #e74c3c;
    }
    #frame #sidepanel #profile .wrap img.offline, #profile .wrap svg.offline {
        border: 2px solid #95a5a6;
    }
    #frame #sidepanel #profile .wrap p {
        float: left;
        margin-left: 15px;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap p {
            display: none;
        }
    }
    #frame #sidepanel #profile .wrap i.expand-button {
        float: right;
        margin-top: 23px;
        font-size: 0.8em;
        cursor: pointer;
        color: #435f7a;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap i.expand-button {
            display: none;
        }
    }
    #frame #sidepanel #profile .wrap #status-options {
        position: absolute;
        opacity: 0;
        visibility: hidden;
        width: 150px;
        margin: 70px 0 0 0;
        border-radius: 6px;
        z-index: 99;
        line-height: initial;
        background: #435f7a;
        -moz-transition: 0.3s all ease;
        -o-transition: 0.3s all ease;
        -webkit-transition: 0.3s all ease;
        transition: 0.3s all ease;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap #status-options {
            width: 58px;
            margin-top: 57px;
        }
    }
    #frame #sidepanel #profile .wrap #status-options.active {
        opacity: 1;
        visibility: visible;
        margin: 75px 0 0 0;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap #status-options.active {
            margin-top: 62px;
        }
    }
    #frame #sidepanel #profile .wrap #status-options:before {
        content: '';
        position: absolute;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 8px solid #435f7a;
        margin: -8px 0 0 24px;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap #status-options:before {
            margin-left: 23px;
        }
    }
    #frame #sidepanel #profile .wrap #status-options ul {
        overflow: hidden;
        border-radius: 6px;
    }
    #frame #sidepanel #profile .wrap #status-options ul li {
        padding: 15px 0 30px 18px;
        display: block;
        cursor: pointer;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap #status-options ul li {
            padding: 15px 0 35px 22px;
        }
    }
    #frame #sidepanel #profile .wrap #status-options ul li:hover {
        background: #496886;
    }
    #frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin: 5px 0 0 0;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
            width: 14px;
            height: 14px;
        }
    }
    #frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
        content: '';
        position: absolute;
        width: 14px;
        height: 14px;
        margin: -3px 0 0 -3px;
        background: transparent;
        border-radius: 50%;
        z-index: 0;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
            height: 18px;
            width: 18px;
        }
    }
    #frame #sidepanel #profile .wrap #status-options ul li p {
        padding-left: 12px;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #profile .wrap #status-options ul li p {
            display: none;
        }
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-online span.status-circle {
        background: #2ecc71;
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-online.active span.status-circle:before {
        border: 1px solid #2ecc71;
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-away span.status-circle {
        background: #f1c40f;
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-away.active span.status-circle:before {
        border: 1px solid #f1c40f;
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-busy span.status-circle {
        background: #e74c3c;
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-busy.active span.status-circle:before {
        border: 1px solid #e74c3c;
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-offline span.status-circle {
        background: #95a5a6;
    }
    #frame #sidepanel #profile .wrap #status-options ul li#status-offline.active span.status-circle:before {
        border: 1px solid #95a5a6;
    }
    #frame #sidepanel #profile .wrap #expanded {
        padding: 100px 0 0 0;
        display: block;
        line-height: initial !important;
    }
    #frame #sidepanel #profile .wrap #expanded label {
        float: left;
        clear: both;
        margin: 0 8px 5px 0;
        padding: 5px 0;
    }
    #frame #sidepanel #profile .wrap #expanded input {
        border: none;
        margin-bottom: 6px;
        background: #32465a;
        border-radius: 3px;
        color: #f5f5f5;
        padding: 7px;
        width: calc(100% - 43px);
    }
    #frame #sidepanel #profile .wrap #expanded input:focus {
        outline: none;
        background: #435f7a;
    }
    #frame #sidepanel #search {
        border-top: 1px solid #32465a;
        border-bottom: 1px solid #32465a;
        font-weight: 300;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #search {
            display: none;
        }
    }
    #frame #sidepanel #search label {
        position: absolute;
        margin: 10px 0 0 20px;
    }
    #frame #sidepanel #search input {
        font-family: "proxima-nova",  "Source Sans Pro", sans-serif;
        padding: 10px 0 10px 46px;
        width: calc(100% - 25px);
        border: none;
        background: #32465a;
        color: #f5f5f5;
    }
    #frame #sidepanel #search input:focus {
        outline: none;
        background: #435f7a;
    }
    #frame #sidepanel #search input::-webkit-input-placeholder {
        color: #f5f5f5;
    }
    #frame #sidepanel #search input::-moz-placeholder {
        color: #f5f5f5;
    }
    #frame #sidepanel #search input:-ms-input-placeholder {
        color: #f5f5f5;
    }
    #frame #sidepanel #search input:-moz-placeholder {
        color: #f5f5f5;
    }
    #frame #sidepanel #contacts {
        height: calc(100% - 177px);
        overflow-y: scroll;
        overflow-x: hidden;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #contacts {
            height: calc(100% - 149px);
            overflow-y: scroll;
            overflow-x: hidden;
        }
        #frame #sidepanel #contacts::-webkit-scrollbar {
            display: none;
        }
    }
    #frame #sidepanel #contacts.expanded {
        height: calc(100% - 334px);
    }
    #frame #sidepanel #contacts::-webkit-scrollbar {
        width: 8px;
        background: #2c3e50;
    }
    #frame #sidepanel #contacts::-webkit-scrollbar-thumb {
        background-color: #243140;
    }
    #frame #sidepanel #contacts ul li.contact {
        position: relative;
        padding: 10px 0 15px 0;
        font-size: 0.9em;
        cursor: pointer;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #contacts ul li.contact {
            padding: 6px 0 46px 8px;
        }
    }
    #frame #sidepanel #contacts ul li.contact:hover {
        background: #32465a;
    }
    #frame #sidepanel #contacts ul li.contact.active {
        background: #32465a;
        border-right: 5px solid #435f7a;
    }
    #frame #sidepanel #contacts ul li.contact.active span.contact-status {
        border: 2px solid #32465a !important;
    }
    #frame #sidepanel #contacts ul li.contact .wrap {
        width: 88%;
        margin: 0 auto;
        position: relative;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #contacts ul li.contact .wrap {
            width: 100%;
        }
    }
    #frame #sidepanel #contacts ul li.contact .wrap span {
        position: absolute;
        left: 0;
        margin: -2px 0 0 -2px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid #2c3e50;
        background: #95a5a6;
    }
    #frame #sidepanel #contacts ul li.contact .wrap span.online {
        background: #2ecc71;
    }
    #frame #sidepanel #contacts ul li.contact .wrap span.away {
        background: #f1c40f;
    }
    #frame #sidepanel #contacts ul li.contact .wrap span.busy {
        background: #e74c3c;
    }
    #frame #sidepanel #contacts ul li.contact .wrap img, #contacts ul li.contact .wrap svg {
        width: 40px;
        border-radius: 50%;
        float: left;
        margin-right: 10px;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #contacts ul li.contact .wrap img, #contacts ul li.contact .wrap svg {
            margin-right: 0px;
        }
    }
    #frame #sidepanel #contacts ul li.contact .wrap .meta {
        padding: 5px 0 0 0;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #contacts ul li.contact .wrap .meta {
            display: none;
        }
    }
    #frame #sidepanel #contacts ul li.contact .wrap .meta .name {
        font-weight: 600;
    }
    #frame #sidepanel #contacts ul li.contact .wrap .meta .preview {
        margin: 5px 0 0 0;
        padding: 0 0 1px;
        font-weight: 400;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        -moz-transition: 1s all ease;
        -o-transition: 1s all ease;
        -webkit-transition: 1s all ease;
        transition: 1s all ease;
    }
    #frame #sidepanel #contacts ul li.contact .wrap .meta .preview span {
        position: initial;
        border-radius: initial;
        background: none;
        border: none;
        padding: 0 2px 0 0;
        margin: 0 0 0 1px;
        opacity: .5;
    }
    #frame #sidepanel #bottom-bar {
        position: absolute;
        width: 100%;
        bottom: 0;
    }
    #frame #sidepanel #bottom-bar button {
        float: left;
        border: none;
        width: 50%;
        padding: 10px 0;
        background: #32465a;
        color: #f5f5f5;
        cursor: pointer;
        font-size: 0.85em;
        font-family: "proxima-nova",  "Source Sans Pro", sans-serif;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #bottom-bar button {
            float: none;
            width: 100%;
            padding: 15px 0;
        }
    }
    #frame #sidepanel #bottom-bar button:focus {
        outline: none;
    }
    #frame #sidepanel #bottom-bar button:nth-child(1) {
        border-right: 1px solid #2c3e50;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #bottom-bar button:nth-child(1) {
            border-right: none;
            border-bottom: 1px solid #2c3e50;
        }
    }
    #frame #sidepanel #bottom-bar button:hover {
        background: #435f7a;
    }
    #frame #sidepanel #bottom-bar button i {
        margin-right: 3px;
        font-size: 1em;
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #bottom-bar button i {
            font-size: 1.3em;
        }
    }
    @media screen and (max-width: 735px) {
        #frame #sidepanel #bottom-bar button span {
            display: none;
        }
    }
    #frame .content {
        float: right;
        width: 60%;
        height: 100%;
        overflow: hidden;
        position: relative;
    }
    @media screen and (max-width: 735px) {
        #frame .content {
            width: calc(100% - 58px);
            min-width: 300px !important;
        }
    }
    @media screen and (min-width: 900px) {
        #frame .content {
            width: calc(100% - 340px);
        }
    }
    #frame .content .contact-profile {
        width: 100%;
        height: 60px;
        line-height: 60px;
        background: #f5f5f5;
    }
    #frame .content .contact-profile img {
        width: 40px;
        border-radius: 50%;
        float: left;
        margin: 9px 12px 0 9px;
    }
    #frame .content .contact-profile p {
        float: left;
    }
    #frame .content .contact-profile .social-media {
        float: right;
    }
    #frame .content .contact-profile .social-media i {
        margin-left: 14px;
        cursor: pointer;
    }
    #frame .content .contact-profile .social-media i:nth-last-child(1) {
        margin-right: 20px;
    }
    #frame .content .contact-profile .social-media i:hover {
        color: #435f7a;
    }
    #frame .content .messages {
        height: auto;
        min-height: calc(100% - 93px);
        max-height: calc(100% - 93px);
        overflow-y: scroll;
        overflow-x: hidden;
    }
    @media screen and (max-width: 735px) {
        #frame .content .messages {
            max-height: calc(100% - 105px);
        }
    }
    #frame .content .messages::-webkit-scrollbar {
        width: 8px;
        background: transparent;
    }
    #frame .content .messages::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.3);
    }
    #frame .content .messages ul li {
        display: inline-block;
        clear: both;
        float: left;
        margin: 15px 15px 5px 15px;
        width: calc(100% - 25px);
        font-size: 0.9em;
    }
    #frame .content .messages ul li:nth-last-child(1) {
        margin-bottom: 20px;
    }
    #frame .content .messages ul li.sent img, .messages ul li.sent svg {
        float:left;
        margin: 6px 8px 0 0;
    }
    #frame .content .messages ul li.sent p {
        background: #435f7a;
        color: #f5f5f5;
    }
    #frame .content .messages ul li.replies img, .messages ul li.replies svg{
        float: right;
        margin: 6px 0 0 8px;
    }
    #frame .content .messages ul li.replies p {
        background: #f5f5f5;
        float: right;
    }
    #frame .content .messages ul li img {
        width: 22px;
        border-radius: 50%;
        float: left;
    }
    #frame .content .messages ul li p {
        display: inline-block;
        padding: 10px 15px;
        border-radius: 20px;
        max-width: 205px;
        line-height: 130%;
    }
    @media screen and (min-width: 735px) {
        #frame .content .messages ul li p {
            max-width: 300px;
        }
    }
    #frame .content .message-input {
        position: absolute;
        bottom: 0;
        width: 100%;
        z-index: 99;
    }
    #frame .content .message-input .wrap {
        position: relative;
    }
    #frame .content .message-input .wrap input {
        font-family: "proxima-nova",  "Source Sans Pro", sans-serif;
        float: left;
        border: none;
        width: calc(100% - 90px);
        padding: 11px 32px 10px 8px;
        font-size: 0.8em;
        color: #32465a;
    }
    @media screen and (max-width: 735px) {
        #frame .content .message-input .wrap input {
            padding: 15px 32px 16px 8px;
        }
    }
    #frame .content .message-input .wrap input:focus {
        outline: none;
    }
    #frame .content .message-input .wrap .attachment {
        position: absolute;
        right: 60px;
        z-index: 4;
        margin-top: 10px;
        font-size: 1.1em;
        color: #435f7a;
        opacity: .5;
        cursor: pointer;
    }
    @media screen and (max-width: 735px) {
        #frame .content .message-input .wrap .attachment {
            margin-top: 17px;
            right: 65px;
        }
    }
    #frame .content .message-input .wrap .attachment:hover {
        opacity: 1;
    }
    #frame .content .message-input .wrap button {
        float: right;
        border: none;
        width: 50px;
        padding: 12px 0;
        cursor: pointer;
        background: #32465a;
        color: #f5f5f5;
    }
    @media screen and (max-width: 735px) {
        #frame .content .message-input .wrap button {
            padding: 16px 0;
        }
    }
    #frame .content .message-input .wrap button:hover {
        background: #435f7a;
    }
    #frame .content .message-input .wrap button:focus {
        outline: none;
    }
</style>
{% endblock %}

{% block js %}
<script>
    /**
     * id - set the ID for SVG element
     * class - set the class name for SVG element
     * firstName: User's first name (String)
     * lastName: User's last naem (String
     * cx: SVG X-Axis coordinate of a center point
     * cy: SVG Y-Axis coordinate of a center point
     * x: SVG <text> X coordinate of the starting point of the text baseline.
     * y: SVG <text> Y coordinate of the starting point of the text baseline.
     */
    function svgNameInitial(id, className, firstName, lastName, cx, cy, r, x, y, fontSize){
         var initialName = firstName.slice(0,1).toUpperCase() + lastName.slice(0,1).toUpperCase();
         svgHtml = "<svg id='" + id + "' width='45' height='45' class='" + className + "'><circle cx='" + cx + "' cy='" + cy + "' r='" + r + "' " +
                 "fill='#aeaeae'/><text x='" + x + "%' y='" + y + "%' text-anchor='middle' fill='white'" +
                 " font-size='" + fontSize + "px'" + " font-family='Arial' dy='.3em'>" + initialName +
                 "</text>Sorry, your browser does not support inline SVG.</svg>";
         return svgHtml;
    }

    function newMessage() {
        message = $(".message-input input").val();
        gameId = 1;

        if($.trim(message) == '') {
            return false;
        }

        $.ajax({
            data: "message=" + message + "&gameId=" + gameId,
            url: "{{url(getToken())}}/addMessage",
            type: "POST",
            success:(function(result){
                let obj = JSON.parse(result);
                if(obj.status === 400){
                    $.toast({
                        heading: 'Error',
                        text: obj.msg,
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-center'
                    });
                }
                else{
                    $('.message-input input').val(null);
                }
            })
        })
    };

    function updateUserLastActivity(){
        $("*").bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function(){
            // Prevent calling of an event handler multiple times.
            $("*").unbind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick');

            // Call the function to bind the event handlers to the "cick" JavaScript event.
            setClick();

            // Call AJAX to update the timestamp in the database
            $.ajax({
               url: "{{url(getToken())}}/updateUserLastActivity",
               type: "POST",
               success: function(result){
                   let obj = JSON.parse(result);

                   if(obj.status !== 200){
                       $.toast({
                           heading: 'Error',
                           text: obj.msg,
                           showHideTransition: 'fade',
                           icon: 'error',
                           position: 'top-center'
                       });
                   }
                   // Call this function every 60 seconds.
                   setTimeout(updateUserLastActivity, 60000);
               }
            });
        });
    }

    function setClick(){

        $("#profile-img, #profile-svg").click(function() {
            $("#status-options").toggleClass("active");
        });

        $(".expand-button").click(function() {
            $("#profile").toggleClass("expanded");
            $("#contacts").toggleClass("expanded");
        });

        $("#status-options ul li").click(function() {
            $("#profile-img, #profile-svg").removeClass();
            $("#status-online").removeClass("active");
            $("#status-away").removeClass("active");
            $("#status-busy").removeClass("active");
            $("#status-offline").removeClass("active");
            $(this).addClass("active");

            if($("#status-online").hasClass("active")) {
                $("#profile-img, #profile-svg").addClass("online");
            } else if ($("#status-away, #profile-svg").hasClass("active")) {
                $("#profile-img, #profile-svg").addClass("away");
            } else if ($("#status-busy, #profile-svg").hasClass("active")) {
                $("#profile-img, #profile-svg").addClass("busy");
            } else if ($("#status-offline").hasClass("active")) {
                $("#profile-img, #profile-svg").addClass("offline");
            } else {
                $("#profile-img, #profile-svg").removeClass();
            };

            $("#status-options").removeClass("active");
        });


        $('.submit').click(function(e) {
            newMessage();
        });

        $("#contacts ul li").click(function() {
            Swal.fire({
                title: "Do you want to play against with " + $(this).find(".name").text() + "?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I am ready.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: (result)=>{
                    $('.swal2-modal').pleaseWait();
                }

            })
        });
    }

    // Handler for .ready() called.
    $(function(){
        //Customize the user's profile image
        $("#profile .wrap").prepend(svgNameInitial("profile-svg", "online", "{{firstname}}", "{{lastname}}", 20, 20, 20, 50, 50, 20));

        // Call the function to bind the event handlers to the "cick" JavaScript event.
        setClick();

        $(window).on('keydown', function(e) {
            if (e.which == 13) {
                newMessage();
                return false;
            }
        });

        var listMessage = new EventSource("{{url(getToken())}}/stream");
        listMessage.onmessage = function(event){
            data = $.parseJSON(event.data);

            // Get all ID from <li>
            let MSG_ID = $( ".messages ul li" )
                .map(function() {
                    return this.id;
                })
                .toArray();

            var html = "";
            $.each(data, function(i, item){
                // Append the messages into the HTML as long as there is no duplicate timestamp ID
                if(!($.inArray(data[i].timestamp, MSG_ID) >= 0)){
                    // Check if the message comes from you, then set the class on it.
                    if(data[i].username === "{{username}}") html+="<li id='" + data[i].timestamp + "'" + "class='replies'>";
                    else html+="<li id='" + data[i].timestamp + "'" + "class='sent'>";

                    html+= svgNameInitial("", "", data[i].firstname, data[i].lastname, 15, 15, 15, 35, 35, 15);
                    html+= "<p><span class='font-black'>" + data[i].firstname + ": </span>" + data[i].message + "</p></li>";
                }
            });

            // Do not append if html is empty
            if(html.trim()){
                //console.log(html);
                $(html).appendTo(".messages ul");
                $(".messages").animate({ scrollTop: $(document).height() }, "fast");
            }
        };

        var onlineUser = new EventSource("{{url(getToken())}}/getOnlineUser");
        onlineUser.onmessage = function(event) {
            data = $.parseJSON(event.data);
            // Get all registered and online users' ID from <li>
            let USER_ID = $( "#contacts ul li" )
                .map(function() {
                    return this.id;
                })
                .toArray();

            var html = "";
            var users = [];
            $.each(data, function(i, item){
                //Add the user's ID to the array.
                users.push(data[i].activitykey);

                //Append the users into the HTML as long as there is no duplicate activitykey ID
                if(!($.inArray(data[i].activitykey, USER_ID) >= 0)){
                    html+="<li id='" + data[i].activitykey + "' class='contact'><div class='wrap'><span class='contact-status online'></span>";
                    html+= svgNameInitial("", "", data[i].firstname, data[i].lastname, 20, 20, 20, 50, 50, 20);
                    html+= "<div class='meta'><p class='name'>" + data[i].firstname + " " + data[i].lastname + "</p>"
                    html+="<p class='preview'></p></div></div></li>";
                }
                else{
                    //Check if there is any away status on the user, then change it to the online.
                    var id = "#" +  data[i].activitykey + " .wrap span";
                    if($(id).hasClass("contact-status away")){
                        $(id).attr("class", "contact-status online");
                    }
                }
            });

            //Change the user's status (away) due to the inactivity
            $.each(USER_ID, function(index, value){
               if(!($.inArray(value, users) >= 0)){
                   var id = "#" + value.toString() + " .wrap span";
                   $(id).attr('class', 'contact-status away');
               }
            });

            // // Do not append if html is empty
            if(html.trim()){
                setClick(); // Reset the setting especially #contacts swal.fire()
                $(html).appendTo("#contacts ul");
            }
        };

        updateUserLastActivity();

    });




</script>
{% endblock %}

{% block content %}
<div id="frame">
    <div id="sidepanel">
        <div id="profile">
            <div class="wrap">
                <p>{{firstname}} {{lastname}}</p>
                <i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>
                <div id="status-options">
                    <ul>
                        <li id="status-online" class="active"><span class="status-circle"></span> <p>Online</p></li>
                        <li id="status-away"><span class="status-circle"></span> <p>Away</p></li>
                        <li id="status-busy"><span class="status-circle"></span> <p>Busy</p></li>
                        <li id="status-offline"><span class="status-circle"></span> <p>Offline</p></li>
                    </ul>
                </div>
                <div id="expanded">
                    <label for="twitter"><i class="fa fa-facebook fa-fw" aria-hidden="true"></i></label>
                    <input name="twitter" type="text" value="mikeross" />
                    <label for="twitter"><i class="fa fa-twitter fa-fw" aria-hidden="true"></i></label>
                    <input name="twitter" type="text" value="ross81" />
                    <label for="twitter"><i class="fa fa-instagram fa-fw" aria-hidden="true"></i></label>
                    <input name="twitter" type="text" value="mike.ross" />
                </div>
            </div>
        </div>
        <div id="search">
            <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
            <input type="text" placeholder="Search contacts..." />
        </div>
        <div id="contacts">
            <ul>
<!--                <li class="contact">-->
<!--                    <div class="wrap">-->
<!--                        <span class="contact-status away"></span>-->
<!--                        <img src="http://emilcarlsson.se/assets/rachelzane.png" alt="" />-->
<!--                        <div class="meta">-->
<!--                            <p class="name">Rachel Zane</p>-->
<!--                            <p class="preview">I was thinking that we could have chicken tonight, sounds good?</p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </li>-->
            </ul>
        </div>
        <div id="bottom-bar">
            <button id="addcontact"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add contact</span></button>
            <button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>
        </div>
    </div>
    <div class="content">
        <div class="contact-profile">
            <img src="https://www.shopbecker.com/globalassets/product-images/mb4430_1_.jpg" alt="" />
            <p>Four Connect</p>
            <div class="social-media">
                <i class="fa fa-facebook" aria-hidden="true"></i>
                <i class="fa fa-twitter" aria-hidden="true"></i>
                <i class="fa fa-instagram" aria-hidden="true"></i>
            </div>
        </div>
        <div class="messages">
            <ul></ul>
        </div>
        <div class="message-input">
            <div class="wrap">
                <input type="text" placeholder="Write your message..." />
                <i class="fa fa-paperclip attachment" aria-hidden="true"></i>
                <button class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
            </div>
        </div>
    </div>
</div>
{% endblock %}