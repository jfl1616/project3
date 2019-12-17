{% extends 'template.php' %}

{% block title %}Four Connect Game{% endblock %}

{% block css %}
<link rel="stylesheet" href="{{asset('css/lobby.css')}}" />
<link rel="stylesheet" href="{{asset('css/fourconnect.css')}}" />
{% endblock %}

{% block js %}
<script>
    {% include 'js/chatroom.js' %}
    {% include 'js/fourconnect.js' %}
</script>
{% endblock %}

{% block content %}
<div id="game-board">
    <div class="bg-blue-100 border-t border-b border-blue-500 text-blue-700 px-4 py-3 text-center" role="alert">
        <p class="font-bold"><span class="currentPlayer"></span>'s turn to make a move.</p>
        <button class="resetGame bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">Start Over</button>
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
