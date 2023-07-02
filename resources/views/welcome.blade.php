

@php

$code = isset($code) ? $code : null;

@endphp



<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
        body{ background:black }

        .spinner-container {
        display: none ! important;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        }

        .spinner-image {
        width: 100px;
        height: 100px;
        display: none;
        }

        </style>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


    </head>

    <body>

        @if(isset($success))
            <div class="alert alert-success">
                {{ $success }}
            </div>
        @endif

        @if($code==null)
            <div style="padding:50px">
                <a class="btn btn-primary" href="https://www.sageone.com/oauth2/auth/central?filter=apiv3.1&response_type=code&client_id=32653905-2064-4cfd-b1ba-d4472b6881b1/d33b217c-7df5-4586-914c-cebfe4cf22cc&redirect_uri=http://127.0.0.1:8000&scope=full_access&state=random_string">ACCEDER A LA APLICACIÓN</a>
            <div>
        @else

        <div class="d-flex flex-wrap">
                <x-options :code="$code" class="flex-grow-0 flex-shrink-0 flex-basis-2"/>
        </div>
        @endif

        <div id='sp' class="spinner-container">
            <img src="{{ asset('img/spinner.gif') }}" alt="Spinner" class="spinner-image">
          </div>




    </body>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script>

    document.addEventListener('DOMContentLoaded', function() {

    let sesionerror = JSON.parse(localStorage.getItem('sesionerror'));

    const urlParams = new URLSearchParams(window.location.search);

    // Obtener la URL actual
    var url = window.location.href;

    if(url.includes('sessionerror')){
       localStorage.setItem("sesionerror", JSON.stringify({
        'from_date': urlParams.get('from_date'),
        'to_date': urlParams.get('to_date'),
        'type': urlParams.get('type')
       }));

       window.location.href = 'https://www.sageone.com/oauth2/auth/central?filter=apiv3.1&response_type=code&client_id=32653905-2064-4cfd-b1ba-d4472b6881b1/d33b217c-7df5-4586-914c-cebfe4cf22cc&redirect_uri=http://127.0.0.1:8000&scope=full_access&state=random_string'
    }

    if(sesionerror !=null && sesionerror !=undefined){
        if( urlParams.get('code')!= null && urlParams.get('code')!=undefined ){

            var spinnerContainer = document.getElementById('sp');
            spinnerContainer.style.display = 'block';


           const form = document.getElementById(sesionerror.type);
           const fromDateInput = form.elements.from_date;
           const toDateInput = form.elements.to_date;

           fromDateInput.value = sesionerror.from_date
           toDateInput.value = sesionerror.to_date

           localStorage.clear();

           form.submit();
        }
    }


});
    </script>
</html>
