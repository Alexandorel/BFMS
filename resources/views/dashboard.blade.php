<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <style>
            html, body {
                height: 100%;
                margin: 0;
            }
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: system-ui, -apple-system, sans-serif;
            }
            h1 {
                font-size: 3rem;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <h1>dashboard</h1>
    </body>
</html>
