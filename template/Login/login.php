{% extends 'template.php' %}

{% block css %}<link rel="stylesheet" href="{{asset('css/main.css')}}" />{% endblock %}

{% block title %}Login & Registration{% endblock %}

{% block js %}
<script>
    {% include 'js/login.js' %}
</script>
{% endblock %}

{% block content %}
<div class="page-wrapper bg-gra-02 p-t-130 p-b-100">
    <div class="wrapper wrapper--w680">
        <div class="card card-4">
            <div class="card-body">
                <form class="register-form" method="POST">
                    <h2 class="title">Register Account</h2>
                    <div class="row row-space">
                        <div class="col-2">
                            <div class="input-group">
                                <label class="label">first name</label>
                                <input class="input--style-4" type="text" name="first_name" id="first_name" value="{{firstname}}">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group">
                                <label class="label">last name</label>
                                <input class="input--style-4" type="text" name="last_name" id="last_name" value="{{lastname}}">
                            </div>
                        </div>
                    </div>
                    <div class="row row-space">
                        <div class="col-2">
                            <div class="input-group">
                                <label class="label">username</label>
                                <input class="input--style-4" type="text" name="username" id="register_username" value="{{username}}">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group">
                                <label class="label">password</label>
                                <input class="input--style-4" type="password" name="password" id="register_password">
                            </div>
                        </div>
                    </div>
                    <div class="row row-space">
                        <div class="col-2">
                            <div class="input-group">
                                <label class="label">confirm password</label>
                                <input class="input--style-4" type="password" name="confirm" id="confirm_password">
                            </div>
                        </div>
                        <div class="input-group">
                            <label class="label">email</label>
                            <input class="input--style-4" type="text" name="email" value="{{email}}">
                        </div>
                    </div>
                    <div class="input-group">
                        <label class="label">country</label>
                        <input type="hidden" name="hidden_country" id="hidden_country" value="{{country}}">
                        <div class="input-group">
                            <div class="rs-select2 js-select-simple select--no-search">
                                <select id="country" name="country">
                                    <option disabled="disabled" selected="selected">Choose option</option>
                                </select>
                                <div class="select-dropdown"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-t-15">
                        <button class="btn btn--radius-2 btn--blue" type="submit">Register</button>
                        <p class="message">Already registered? <a class="login text-blue-700" href="#">Sign In</a></p>
                    </div>
                </form>
                <form class="login-form" method="POST">
                    <h2 class="title">Login Account</h2>
                    <div class="row row-space">
                        <div class="col-2">
                            <div class="input-group">
                                <label class="label">username</label>
                                <input class="input--style-4" type="text" name="username" id="login_username">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group">
                                <label class="label">password</label>
                                <input class="input--style-4" type="password" name="password" id="login_password">
                            </div>
                        </div>
                    </div>
                    <div class="p-t-15">
                        <p class="message"><a class ="text-blue-700" href="">Forget Password?</a></p>
                        <button class="btn btn--radius-2 btn--blue" type="submit">Login</button>
                        <p class="message">Not registered? <a class="register text-blue-700" href="#">Create an account</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="unsupported bg-gra-02 p-t-130 p-b-100">
    <div class="wrapper wrapper--w680">
        <div class="container">
            <div class="container-wrapper">
                <div class="container-full-column">
                    <div class="unsupported-browser">

                        <h1>Please upgrade your browser in order to use HTML5 Server-Sent Events (SSE).</h1>
                        <p>
                            The Server-sent event is heavily using in this project when a web page automatically get updates from a server. It gives you a better experience with new functions. Unfortunately, your browser does not support this feature at this time. The more information can be be found at <a href="https://www.w3schools.com/html/html5_serversentevents.asp">HTML5 Server-Sent Events - W3Schools</a>.
                        </p>
                        <h2>Please download one of these free and up-to-date browsers:</h2>
                        <ul>
                            <li><a href="https://www.mozilla.com/firefox/" target="_blank">Firefox</a></li>
                            <li><a href="https://www.google.com/chrome/browser/desktop/" target="_blank">Chrome</a></li>
                            <li><a href="https://support.apple.com/downloads/safari" target="_blank">Safari</a></li>
                            <li><a href="https://www.opera.com/" target="_blank">Opera</a></li>
                        </ul>
                        <hr>

                        <div class="unsupported-message">
                            <h3>I can't update my browser</h3>
                            <ul>
                                <li>Ask your system administrator to update your browser if you cannot install updates yourself.</li>
                                <li>If you can't change your browser because of compatibility issues, think about installing a second browser for utilization of this site and keep the old one for compatibility.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{% endblock %}