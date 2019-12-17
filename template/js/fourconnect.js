(function() {
    // slight update to account for browsers not supporting e.which
    //Prevent the user to refresh the page by pressing down the F5 key on the keyboard
    function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };
    // To disable f5
    /* jQuery < 1.7 */
    $(document).bind("keydown", disableF5);
    /* OR jQuery >= 1.7 */
    $(document).on("keydown", disableF5);

    //Enable the handler to alert the user when he/she attempts to reload the page
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
         * RESET GAME
         **********************************/
        //Purpose: This page automatically gets updates from a server regarding
        //the incoming request for the reset game.
        var checkResetGame = new EventSource("{{url(getToken())}}/{{gameId}}/checkResetGame");
        checkResetGame.onmessage = function(event) {
            var data = $.parseJSON(event.data);
            var response = "";

            if(data.length !== 0){

                //A way to see if there is already a modal open.
                if (!Swal.isVisible()) {
                    Swal.fire({
                        title: data[0].msg,
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
                        if(result.value){
                            response = "reload";
                        }
                        //Opponent has rejected the request.
                        else if(result.dismiss === Swal.DismissReason.cancel){
                            response = "reject";
                        }

                        //Execute Ajax to insert the response into the database
                        $.ajax({
                            data: "response=" + response,
                            url: "{{url(getToken())}}/{{gameId}}/responseResetGame",
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
                    })
                }
            }
        };
        //Purpose: This page automatically gets updates from a server regarding
        //the approval to start over the game.
        var reloadResetGame = new EventSource("{{url(getToken())}}/{{gameId}}/reloadResetGame");
        reloadResetGame.onmessage = function(event) {
            var data = $.parseJSON(event.data);
            console.log(data);

            if(data.length !== 0 && data[0].eligibility === "approval"){
                window.onbeforeunload = function () {
                    // blank function do nothing
                };

                Swal.fire({
                    type: "info",
                    title: "Start Over",
                    text: data[0].msg,
                }).then(function(){
                    location.reload();
                });
            }
        };

        /*********************************
         * DROP A CHIP
         **********************************/

        //Purpose: This page automatically gets updates from a server regarding
        //the information of username, location, and chip color.
        var playerMove = new EventSource("{{url(getToken())}}/{{gameId}}/playerMove");
        playerMove.onmessage = function(event) {
            var data = $.parseJSON(event.data);
            console.log(data);

            //Update the current player's name to notify both players
            let currentPlayer = $(".currentPlayer").text();
            if(currentPlayer !== data.playerName){
                $(".currentPlayer").text(data.playerName);
            }
            //Make sure, the username is matched, the chipColor must be opposite, and the location must have a value.
            if(data.username === "{{username}}" && data.chipColor !== "{{chipColor}}" && data.location !== ""){

                // Call setChipColor function to drop a chip
                setChipColor(data.location, data.chipColor);

                //Count the num of the turns to determine the tie game purpose.
                numTurns = numTurns + 1;

                // Secondly, do AJAX to call Chip Class to switch the color.
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

        //Purpose: This page automatically gets updates from a server regarding
        //the result of the game.
        var gameStatus = new EventSource("{{url(getToken())}}/{{gameId}}/gameStatus");
        gameStatus.onmessage = function(event) {
            var data = $.parseJSON(event.data);
            if(data.length !== 0){
                //Stop the EventSources since it receives the status.
                playerMove.close();
                gameStatus.close();

                //A way to see if there is already a modal open.
                if (!Swal.isVisible()) {
                    //Cancel the event handler due to the result of the game.
                    window.onbeforeunload = function () {
                        // blank function do nothing
                    };
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

        //Prepare the game, such as binding the event handler (click).
        _init = function() {
            var columns;
            columns = document.querySelectorAll('.column');

            //binding the event handler.
            Array.prototype.forEach.call(columns, function(col) {
                col.addEventListener('click', function() {
                    markNextFree(col.getAttribute('data-x'));
                });
            });

            for(var x = 0; x <= numRows; x++) {
                gameBoard[x] = {};

                //Set up all spaces as unoccupied
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
                data: "location=" + x,
                url: "{{url(getToken())}}/{{gameId}}/click",
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

        //Call Ajax to update the result of game in the database
        var updateGameResult = function(result){
            $.ajax({
                data: "result=" + result,
                url: "{{url(getToken())}}/{{gameId}}/updateGameResult",
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
        };

        // Return true if the game is literally tie by comparing the value
        // between the number of turns and the number of rows multiply by number
        //of columns.
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

        //Return the boolean by validating the bound through 2-D array.
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
                while( isBounds(currentX + (coords[0] * i), currentY + (coords[1] * i)) &&
                    (gameBoard[currentX + (coords[0] * i)][currentY + (coords[1] * i)] === colorChip)
                    ) {
                    chainLength = chainLength + 1;
                    i = i + 1;
                };

            });
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

    ConnectFour(); //Set up the game
})();