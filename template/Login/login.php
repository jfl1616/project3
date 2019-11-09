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
                        <button class="btn btn--radius-2 btn--blue" type="submit" name="btnCreate">Register</button>
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
                        <p class="message"><a class ="text-blue-700" href="reset.php">Forget Password?</a></p>
                        <button class="btn btn--radius-2 btn--blue" type="submit" name="btnLogin">Login</button>
                        <p class="message">Not registered? <a class="register text-blue-700" href="#">Create an account</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{% endblock %}