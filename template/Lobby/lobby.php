{% extends 'template.php' %}

{% block title %}Lobby{% endblock %}

{% block css %}<link rel="stylesheet" href="{{asset('css/lobby.css')}}" />{% endblock %}

{% block js %}
<script>{% include 'js/chatroom.js' %}</script>
<script>
    /*********************************
     * SHOW ALL ONLINE USERS
     **********************************/

    //Purpose: This page automatically gets updates from a server regarding
    //the information of all online players.
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

        //Change the user's status (away) due to the idle.
        $.each(USER_ID, function(index, value){
            if(!($.inArray(value, users) >= 0)){
                var id = "#" + value.toString() + " .wrap span";
                $(id).attr('class', 'contact-status away');
            }
        });

        // Do not append if html is empty
        if(html.trim()){
            $(html).appendTo("#contacts ul");
            setClick(); // Reset the setting especially #contacts swal.fire()
        }
    };

    /*********************************
     * ACCEPT / REJECT BUTTON
     **********************************/

    //Purpose: Notify the user right away when the challenger has requested to play against with this user.
    var incomingChallenge = new EventSource("{{url(getToken())}}/getIncomingChallenge");
    incomingChallenge.onmessage = function(event) {
        data = $.parseJSON(event.data);
        //Make sure, there is a valid incoming request from the challenger.
        // First come, first served
        if (data.length > 0) {
            var challengerName = data[0].player1;
            var challengeId = data[0].challengeId;
            var response = "";

            //A way to see if there is already a modal open.
            if (!Swal.isVisible()) {
                Swal.fire({
                    title: challengerName + " wants to challenge with you.",
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Accept',
                    cancelButtonText: "Reject",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    $('.swal2-modal').pleaseWait();

                    //Opponent has accepted the request.
                    if(result.value)
                        response = "accept";
                    //Opponent has rejected the request.
                    else if(result.dismiss === Swal.DismissReason.cancel)
                        response = "reject";

                    $.ajax({
                        data: "response=" + response + "&challengeId=" + challengeId,
                        url: "{{url(getToken())}}" + "/responseChallenge",
                        type: "POST",
                        success: function(response){
                            let obj = JSON.parse(response);
                            console.log(obj);

                            if(obj.status !== 200){
                                $.toast({
                                    heading: 'Error',
                                    text: obj.msg,
                                    showHideTransition: 'fade',
                                    icon: 'error',
                                    position: 'top-center'
                                });
                            }
                        }
                    });
                })
            }
        }
    };

    /*********************************
     * START GAME BUTTON
     **********************************/
    //Purpose: Notify the challenger and opponent, the game may begins.
    var startGame = new EventSource("{{url(getToken())}}/startGame");
    startGame.onmessage = function(event) {
        data = $.parseJSON(event.data);
        //Make sure that there is a valid game in order to get start.
        // First come, first served
        if (data.length > 0) {
            //A way to see if there is already a modal open.
            if (!Swal.isVisible()) {
                var url = data[0].url;
                Swal.fire({
                    title: data[0].challengerName + " is ready to challenge with you.",
                    imageUrl: 'https://img.icons8.com/small/96/000000/battle.png',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Start Game',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    preConfirm: (result)=>{
                        location.href = url;
                    }
                });
            }
        }
    }
</script>
{% endblock %}

{% block content %}
<div id="frame">
    <div id="sidepanel">
        <div id="profile">
            <div class="wrap">
                <p>{{firstname}} {{lastname}}</p>
                <div id="status-options">
                    <ul>
                        <li id="status-online" class="active"><span class="status-circle"></span> <p>Online</p></li>
                        <li id="status-away"><span class="status-circle"></span> <p>Away</p></li>
                        <li id="status-busy"><span class="status-circle"></span> <p>Busy</p></li>
                        <li id="status-offline"><span class="status-circle"></span> <p>Offline</p></li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="search">
            <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
            <input type="text" placeholder="Search contacts..." />
        </div>
        <div id="contacts">
            <ul></ul>
        </div>
        <div id="bottom-bar">
            <button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>
        </div>
    </div>
    <div class="content">
        <div class="contact-profile">
            <img src="https://www.shopbecker.com/globalassets/product-images/mb4430_1_.jpg" alt="" />
            <p>Four Connect</p>
            <div class="log-out">
                <a href="{{url(getToken())}}/logout"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
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