{% extends 'template.php' %}

{% block title %}Four Connect Game{% endblock %}

{% block css %}
<link rel="stylesheet" href="{{asset('css/lobby.css')}}" />

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
        width: 100%;
        min-width: 360px;
        max-width: 1000px;
        height: 28vh;
        min-height: 0px;
        max-height: 720px;
        background: #E6EAEA;
    }
    #frame .content {
        float: right;
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: relative;
    }
    * {
        border: 0;
        padding: 0;
    }

    #game-board {
        background: #0074B3;
        width: 730px;
        cursor: pointer;
    }

    .column {
        width: 100px;
        display: inline-block;
    }

    @media screen and (max-width: 800px){
        #game-board {
            width: 520px;
        }
        svg{
            height: 8vh;
        }
        .column{
            width: 70px;
        }
    }

    @media screen and (max-width: 600px){
        #game-board {
            width: 427px;
        }
        svg{
            height: 7vh;
        }
        .column{
            width: 56px;
        }
    }

    @media screen and (max-width: 505px){
        #game-board {
            width: 336px;
        }
        svg{
            height: 5vh;
        }
        .column{
            width: 44px;
        }
        #frame{
            min-width:336px;
        }
    }

    @media screen and (max-width: 445px){
        #game-board {
            width: 300px;
        }
        svg{
            height: 4.5vh;
        }
        .column{
            width: 38px;
        }
        #frame{
            min-width:300px;
        }
    }

    @media screen and (max-width: 360px){
        #game-board {
            width: 300px;
        }
        svg{
            height: 4.5vh;
        }
        .column{
            width: 38px;
        }
        #frame{
            height: 40vh;
            min-width:300px;
        }
    }

    .column:hover  circle.free{
        fill: #D5E4ED;
    }

    circle.free {
        fill: #fff;
    }

    circle.red {
        fill: #D50000;
    }

    circle.blue {
        fill: #003fda;
    }

</style>
{% endblock %}
{% block js %}
<script>
    {% include 'js/chatroom.js' %}
</script>
<script>

    (function() {
        // slight update to account for browsers not supporting e.which
        function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };
        // To disable f5
        /* jQuery < 1.7 */
        $(document).bind("keydown", disableF5);
        /* OR jQuery >= 1.7 */
        $(document).on("keydown", disableF5);

        window.onbeforeunload = function() {
            return "WARNING: Are you sure that you want to forfeit this game automatically if you reload the page?";
        };

        var ConnectFour = function() {
            nextY = ""; //Y location
            gameBoard = {};
            colorChip = '{{chipColor}}';
            numRows = 6;
            numCols = 7;
            numTurns = 0;

            /*********************************
             * DROP A CHIP
             **********************************/
            var playerMove = new EventSource("{{url(getToken())}}/{{gameId}}/playerMove");
            playerMove.onmessage = function(event) {
                var data = $.parseJSON(event.data);

                //Update the current player's name to notify both players
                let currentPlayer = $(".currentPlayer").text();
                console.log(currentPlayer);
                console.log(data.playerName);
                console.log(currentPlayer !== data.playerName);

                if(currentPlayer !== data.playerName){
                    $(".currentPlayer").text(data.playerName);
                }

                if(data.username === "{{username}}" && data.chipColor !== "{{chipColor}}" && data.location !== ""){

                    // Call setChipColor function to drop a chip
                    setChipColor(data.location, data.chipColor);

                    //Count the num of the turns to determine the tie game purpose.
                    numTurns = numTurns + 1;

                    // Secondly, do AJAX to call Chip Class to switch the color...
                    $.ajax({
                        data: "chipId={{gameId}}",
                        url: "{{url(getToken())}}/{{gameId}}/switchChipColor",
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
                        }
                    });
                }
            };

            /*********************************
             * CHECK THE STATUS OF A GAME.
             **********************************/
            var gameStatus = new EventSource("{{url(getToken())}}/{{gameId}}/gameStatus");
            gameStatus.onmessage = function(event) {
                var data = $.parseJSON(event.data);
                // console.log(data);
                if(data.length !== 0){
                    //Stop the EventSources since it receives the status.
                    playerMove.close();
                    gameStatus.close();

                    //A way to see if there is already a modal open.
                    if (!Swal.isVisible()) {
                        Swal.fire({
                            type: "info",
                            title: "Result of the game",
                            text: data.status,
                        }).then(function(){
                            location.replace(data.url);
                        });
                    }
                }
            };


            _init = function() {
                var columns;
                columns = document.querySelectorAll('.column');

                Array.prototype.forEach.call(columns, function(col) {
                    col.addEventListener('click', function() {
                        markNextFree(col.getAttribute('data-x'));
                    });
                });

                for(var x = 0; x <= numRows; x++) {
                    gameBoard[x] = {};

                    for(var y = 0; y <= numCols; y++) {
                        gameBoard[x][y] = 'free';
                    }
                }

            };

            var markNextFree = function(x) {
                // Check if there is any free space in this column
                if(!isColumnFree(x)){
                    $.toast({
                        heading: 'Error',
                        text: "No free spaces in this column. Try another.",
                        showHideTransition: 'fade',
                        icon: 'error',
                        position: 'top-center'
                    });
                    return false;
                }
                // Make sure that it's player's turn to make a move; otherwise, the action will be rejected.
                $.ajax({
                    data: "gameId={{gameId}}&location=" + x,
                    url: "{{url(getToken())}}" + "/click",
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
                            return false;
                        }
                        else{
                            setChipColor(x, colorChip); //Go ahead to drop a chip on the column
                            numTurns = numTurns + 1;

                            // Check if opponent won the game.
                            if(isWinner(parseInt(x), nextY)){
                                updateGameResult("{{username}}");
                            }
                            // Check if game is tie, then execute updateGameResult function.
                            else if(isTie()){
                                updateGameResult("tie");
                            }
                        }
                    }
                });

            };

            var updateGameResult = function(result){
                $.ajax({
                    data: "result=" + result,
                    url: "{{url(getToken())}}/{{gameId}}/updateGameResult",
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
            };

            var isTie = function(){
                return numTurns >= numRows * numCols;
            };

            /* Return boolean if the player wins */
            var isWinner = function(currentX, currentY) {
                return checkDirection(currentX, currentY, 'vertical') ||
                    checkDirection(currentX, currentY, 'leftToRightDiagonal') ||
                    checkDirection(currentX, currentY, 'RightToLeftDiagonal') ||
                    checkDirection(currentX, currentY, 'horizontal');
            };


            // Return the available location on Y column
            var getNextY = function(x){
                var nextY;
                nextY = false;

                for(var y = 0; y < numRows; y++) {
                    if(gameBoard[x][y] === 'free') {
                        nextY = y;
                        break;
                    }
                }
                return nextY;
            };

            // Return the boolean if there is any available on column
            var isColumnFree = function(x){
                var nextY = getNextY(x);

                if(!isNaN(nextY)){
                    return true;
                }
                return false;
            };

            // Drop a color chip on the specific column
            var setChipColor = function(x, colorChip){
                nextY = getNextY(x);

                gameBoard[x][nextY] = colorChip.toString();
                document.querySelector('#column-'+x+' .row-'+nextY+' circle').setAttribute(
                    'class', colorChip
                );
                return true;
            };

            var isBounds = function(x, y) {
                return (gameBoard.hasOwnProperty(x) && typeof gameBoard[x][y] !== 'undefined');
            };

            // Determine if player hits any one of the directions (4 in row)
            var checkDirection = function(currentX, currentY, direction) {

                var chainLength, directions;

                directions = {
                    horizontal: [
                        [0, -1], [0, 1]
                    ],
                    vertical: [
                        [-1, 0], [1, 0]
                    ],
                    leftToRightDiagonal: [
                        [-1, -1], [1, 1]
                    ],
                    RightToLeftDiagonal: [
                        [1, -1], [-1, 1]
                    ]
                };

                chainLength = 1;

                directions[direction].forEach(function(coords) {
                    var i = 1;
                    console.log("Current X: " + currentX);
                    console.log("Current Y: " + currentY);
                    console.log(isBounds(currentX + (coords[0] * i), currentY + (coords[1] * i)) && gameBoard[currentX + (coords[0] * i)][currentY + (coords[1] * i)] === colorChip);
                    while( isBounds(currentX + (coords[0] * i), currentY + (coords[1] * i)) &&
                        (gameBoard[currentX + (coords[0] * i)][currentY + (coords[1] * i)] === colorChip)
                        ) {
                        chainLength = chainLength + 1;
                        i = i + 1;
                    };

                });
                console.log("your Color: "+ colorChip);
                console.log("chainLength: " + chainLength);
                console.log(gameBoard);
                return (chainLength >= 4);

            };

            // Reset the board
            var clearBoard = function() {
                Array.prototype.forEach.call(document.querySelectorAll('circle'), function(piece) {
                    piece.setAttribute('class', 'free');
                });

                gameBoard = {};

                for(var x = 0; x <= numRows; x++) {

                    gameBoard[x] = {};

                    for(var y = 0; y <= numCols; y++) {
                        gameBoard[x][y] = 'free';
                    }
                }
                numTurns = 0;
                return gameBoard;
            };

            _init();

        };

        ConnectFour();
    })();

</script>
{% endblock %}


{% block content %}
<div id="game-board">
    <div class="bg-blue-100 border-t border-b border-blue-500 text-blue-700 px-4 py-3 text-center" role="alert">
        <p class="font-bold"><span class="currentPlayer"></span>'s turn to make a move.</p>
    </div>
    <div class="column" id="column-0" data-x="0">
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-5">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-4">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-3">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-2">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-1">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-0">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
    </div>
    <div class="column"  id="column-1" data-x="1">
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-5">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-4">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-3">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-2">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-1">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-0">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
    </div>
    <div class="column" id="column-2" data-x="2">
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-5">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-4">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-3">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-2">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-1">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-0">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
    </div>
    <div class="column" id="column-3" data-x="3">
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-5">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-4">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-3">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-2">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-1">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-0">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
    </div>
    <div class="column" id="column-4" data-x="4">
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-5">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-4">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-3">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-2">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-1">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-0">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
    </div>
    <div class="column" id="column-5" data-x="5">
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-5">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-4">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-3">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-2">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-1">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-0">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
    </div>
    <div class="column" id="column-6" data-x="6">
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-5">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-4">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-3">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-2">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-1">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
        <svg version="1.1" viewBox="0 0 500 500" preserveAspectRatio="xMinYMin meet" height="100" width="100" class="row-0">
            <circle cx="250" cy="250" r="200" class="free"/>
        </svg>
    </div>
    <div id="frame">
        <div class="content">
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
</div>
{% endblock %}
