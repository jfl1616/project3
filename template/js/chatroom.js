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

    if($.trim(message) == '') {
        return false;
    }

    $.ajax({
        data: "message=" + message + "&gameId={{gameId}}",
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
                    // $.toast({
                    //     heading: 'Error',
                    //     text: obj.msg,
                    //     showHideTransition: 'fade',
                    //     icon: 'error',
                    //     position: 'top-center'
                    // });
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

                var opponentId = $(this)[0].id; // the opponent's ID.

                $.ajax({
                    data: "opponentId=" + opponentId,
                    url: "{{url(getToken())}}" + "/sendChallenge",
                    type: "POST",
                    success: function(response){
                        let obj = JSON.parse(response);

                        if(obj.status !== 200){
                            $.toast({
                                heading: 'Error',
                                text: obj.msg,
                                showHideTransition: 'fade',
                                icon: 'error',
                                position: 'top-center'
                            });
                        }
                        else{
                            $.toast({
                                heading: "The opponent has been notified.",
                                text: obj.msg,
                                showHideTransition: 'fade',
                                icon: 'success',
                                position: 'top-center'
                            });
                        }
                    }
                });
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

    var listMessage = new EventSource("{{url(getToken())}}" + "/{{gameId}}/stream");
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
    updateUserLastActivity();
});