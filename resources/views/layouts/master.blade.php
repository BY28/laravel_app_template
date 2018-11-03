<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
        <link rel="stylesheet" href="{{asset('css/animate.css')}}">

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


        <title>Laravel</title>
        
        <script src="https://js.stripe.com/v3/"></script>
    </head>
    <body>
        
            <div id="app">

                <navbar></navbar>
                <vue-progress-bar></vue-progress-bar>
                <transition name="router-anim">
                    <router-view></router-view>
                </transition>

            </div>

        <script src="{{ asset('js/app.js') }}"></script>
        
        <!-- Compiled and minified JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
        <!-- <script src="https://www.paypalobjects.com/api/checkout.js"></script> -->

    </body>
</html>
