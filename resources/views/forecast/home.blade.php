<!DOCTYPE html>
<html>
    <head>
    <title>{{ __('weatherbyip::webview.header') }}</title>
    <link rel="stylesheet" href="{{ asset('packages/weatherbyip/css/materialize.min.css') }}" />
    </head>
    <nav>
        <div class="nav-wrapper blue darken-4">
        <a href="#" class="brand-logo">&nbsp;{{ __('weatherbyip::webview.header') }}</a>
        </div>
    </nav>
    <body>
        <br><br>
        <div class="container">
            <div>
                <form id="ip-form" onSubmit="return false;">
                    <div>
                        <label>{{ __('weatherbyip::webview.enter_ip') }}</label>
                        <input type="text" id="ip-input" value="{{$ip}}" />
                    </div>  
                </form>
                <button onclick="getForecast();" id="ip-form-btn" class="waves-effect waves-light btn">{{ __('weatherbyip::webview.get_forecast') }}</button>

                <div id="forecast-container"></div>
            </div>
        </div>
    </body>

    <script src="{{ asset('packages/weatherbyip/css/js/materialize.min.js') }}"></script>
    <script>
        //If enter key pressed on IP input, get forecast
        document.getElementById('ip-input').addEventListener('keyup',function(e) {
            e.preventDefault();
            if(e.keyCode == 13) {
                getForecast();
            }
        });
        
        function getForecast() {
            var ipForm = document.getElementById('ip-form');
            document.getElementById('forecast-container').innerHTML = '<br><br><div class="progress"><div class="indeterminate"></div></div>';
            //Construct GET AJAX request
            fetch("{{route('forecast.get')}}"+"/"+ipForm[0].value)
                .then(function (response) {
                    if(!response.ok) {
                        return '<br><br><i class="red-text text-darken-3">{{ __("weatherbyip::messages.lookup_fail") }}</i>';
                    }
                    // The API call was successful!
                    return response.text();
                }).then(function (html) {
                    document.getElementById('forecast-container').innerHTML = html;
                })
                .catch(function (err) {
                    console.log(err);
                    // There was an error
                    console.warn("{{ __('weatherbyip::messages.something_went_wrong')}}", err);
                });
        }
    </script>
</html>