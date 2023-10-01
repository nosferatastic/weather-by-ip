<li class="collection-item avatar">
    <img src="{{ asset('packages/weatherbyip/forecast_icons/'.$forecastDay->weather_code.'.png') }}" alt="" class="circle blue large">
    <span class="title">{{date('d M Y',strtotime($forecastDay->forecast_date))}}</span>
    <p>
        <strong>{{ __('weatherbyip::forecasts.'.$forecastDay->weather_code) }}</strong>
        <br>
        {{$forecastDay->temp_low}} - {{$forecastDay->temp_high}} C
    </p>
</li>