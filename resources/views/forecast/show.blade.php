@if(!$location)
    <!-- Error -->
    <br><br>
    <i class="red-text text-darken-3">{{ __('weatherbyip::messages.lookup_fail') }}</i>
@else
    <h4>{{ __('weatherbyip::webview.five_day_forecast_for')}} IP {{$ip}}</h4>
    <p><h5><strong>{{ __('weatherbyip::webview.location') }}:</strong> {{$location->location}}</h5></p>
    <ul class="collection">
        @foreach($forecast as $forecastDay)
            @include('weatherbyip::forecast.partials.forecast_day')
        @endforeach
    </ul>
@endif