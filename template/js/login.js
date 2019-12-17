(function ($) {
    /**
     * Make sure, the user has the up-to-date up-to-date browsers due to the HTML5 Server-Sent Events function.
     * This function will detect the unsupported browser version and show specific duv with message.
     * Credit: https://stackoverflow.com/questions/49686741/detect-unsupported-browser-version-and-show-specific-div-with-message
     */
    navigator.sayswho = ( function () {
        var ua = navigator.userAgent, tem,
            M = ua.match( /(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i ) || [];
        if ( /trident/i.test( M[1] ) ) {
            tem = /\brv[ :]+(\d+)/g.exec( ua ) || [];
            return 'IE ' + ( tem[1] || '' );
        }
        if ( M[1] === 'Chrome' ) {
            tem = ua.match( /\b(OPR|Edge)\/(\d+)/ );
            if ( tem != null ) return tem.slice( 1 ).join( ' ' ).replace( 'OPR', 'Opera' );
        }
        M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
        if ( ( tem = ua.match( /version\/(\d+)/i ) ) != null ) M.splice( 1, 1, tem[1] );
        return M.join( ' ' );
    } )();

    var str = navigator.sayswho;
    var browser = str.substring( 0, str.indexOf( " " ) );
    var version = str.substring( str.indexOf( " " ) );
    version = version.trim();
    version = parseInt( version );
    // console.log( browser );
    // console.log( version );

    if ( ( browser == "Chrome" && version < 70 ) || ( browser == "Firefox" && version < 53 ) || ( browser == "Safari" && version < 5 ) || ( browser == "IE" ) || ( browser == "Opera" && version < 52 ) || browser == "Edge") {
        $(".unsupported").show();
        $(".page-wrapper").hide();
    }

    /*[ Select 2 Config ]
        ===========================================================*/
    try {
        var selectSimple = $('.js-select-simple');
    
        selectSimple.each(function () {
            var that = $(this);
            var selectBox = that.find('select');
            var selectDropdown = that.find('.select-dropdown');
            selectBox.select2({
                dropdownParent: selectDropdown
            });
        });
    
    } catch (err) {
        console.log(err);
    }

    /*[ END Select 2 Config ]
    ===========================================================*/
    // Populate select elements dynamically from json data file
    $.get("{{asset('data/countries.json')}}", function( jsonArray ){
        let html = "";
        let country = document.getElementById("hidden_country");
        $.each(jsonArray, function(index, jsonObject){
            if(jsonObject.name == country.value) {
                html += "<option value='" + jsonObject.name + "' selected='selected'>" + jsonObject.name + "</option>";
                return; // //this is equivalent of 'continue' for jQuery loop
            }
            html+= "<option value='" + jsonObject.name + "'>" + jsonObject.name + "</option>";

        });
        $("#country").append(html);
    });

    // Call Ajax to insert the account into the database
    $('.register-form').submit(function(e)
    {
        e.preventDefault();
        $('body').pleaseWait();

        $.ajax({
            data:$(this).serialize(),
            url: "{{url(getToken())}}/register",
            type:"POST",
            success:function(result)
            {
                let obj = JSON.parse(result);
                if(obj.status === 200){
                    console.log("{{url(getToken())}}/register");
                    $('body').pleaseWait('stop');
                    sweet("success", "Registration - successful", obj.msg, "");
                    setLocalStorageItem("register-show", "false");
                    showRegisterForm();
                }
                else{
                    $('body').pleaseWait('stop');
                    sweet("error", "Registration - Oops", obj.msg);
                }
            }
        })
    });

    $('.login-form').submit(function(e)
    {
        e.preventDefault();
        $.ajax({
            data:$(this).serialize(),
            url: "{{url(getToken())}}/login",
            type: "POST",
            success: function(result){
                let obj = JSON.parse(result)
                if(obj.status === 400){
                    sweet("error", "Login - Oops", obj.msg);
                }
                else{
                    location.href = obj.url;
                }
            }
        });
    });

    // Determine which form it supposes to show the user.
    showRegisterForm();

    // Binding the event handle that will be triggered when the user clicks "Register" or "Login" button.
    $('.message a').click(function(){
        /* Store state in localStorage by setting it to true */
        $(this).hasClass("register") ? setLocalStorageItem("register-show", "true") : setLocalStorageItem("register-show", false);
        $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
    });
    // Change the link color when the user hovers on it.
    $('.message a').mouseover(function (){
        $(this).removeClass("text-blue-700");
        $(this).addClass("text-red-600");
    }).mouseout(function(){
        $(this).removeClass("text-red-600");
        $(this).addClass("text-blue-700", 3000);
    });

    /**
     * Create the data item inside local storage.
     */
    function setLocalStorageItem(keyName, keyValue){
        localStorage.setItem(keyName, keyValue);
    }

    // Should the register form is being showing to the user?
    function showRegisterForm(){
        if(localStorage.getItem("register-show") === "true"){
            $(".login-form").hide();
            $(".register-form").show("slow");
        }
        else{
            $(".register-form").hide().trigger("reset");
            $(".login-form").show("slow");
        }
    }
})(jQuery);