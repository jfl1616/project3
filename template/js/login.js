(function ($) {
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

    // Determine which form it supposes to show the user.
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
                    // let link = "{{url('lobby')}}";
                    // alert(link);
                    // return ;
                    location.href = obj.url;
                }
            }
        });
    });

    showRegisterForm();

    // Handle the action when the user clicks "Register" or "Login" button
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