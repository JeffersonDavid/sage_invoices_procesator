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
        </style>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


    </head>

    <body>



        @if($code==null)

            <div style="padding:50px">
                <a class="btn btn-primary" href="https://www.sageone.com/oauth2/auth/central?filter=apiv3.1&response_type=code&client_id=32653905-2064-4cfd-b1ba-d4472b6881b1/d33b217c-7df5-4586-914c-cebfe4cf22cc&redirect_uri=http://127.0.0.1:8000&scope=full_access&state=random_string">ACCEDER A LA APLICACIÓN</a>
            <div>
        @else

        <div class="container">
            <form method="POST" action="/process?code={{$code}}&country=ES&state=random_string">
              @csrf
                <div style="margin: 50px">
                    <div class="form-group">
                    <label for="from_date">Desde</label>
                    <input type="date" id="from_date" name="from_date">
                    <label for="to_date">Hasta</label>
                    <input type="date" id="to_date" name="to_date">
                    <button type="submit" class="btn btn-primary" style="margin-left:5%">Procesar y enviar facturas a gestoria</button>
                    </div>
            </div>

        </form>
        <div>

        @endif

    </body>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
</html>
