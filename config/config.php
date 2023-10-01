<?php

return [
  'url_prefix' => 'weatherbyip',
  'middleware' => ['web'],
  'http_driver' => 'guzzle',
  // Set above to anything but guzzle to use cURL
  'tables' => [
    'location_lookups' => 'location_lookups',
    'forecasts' => 'forecasts'
  ],
];
