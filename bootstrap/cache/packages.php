<?php return array (
  'barryvdh/laravel-dompdf' => 
  array (
    'providers' => 
    array (
      0 => 'Barryvdh\\DomPDF\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Pdf' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
      'PDF' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
    ),
  ),
  'cviebrock/eloquent-sluggable' => 
  array (
    'providers' => 
    array (
      0 => 'Cviebrock\\EloquentSluggable\\ServiceProvider',
    ),
  ),
  'dougsisk/laravel-country-state' => 
  array (
    'providers' => 
    array (
      0 => 'DougSisk\\CountryState\\CountryStateServiceProvider',
    ),
  ),
  'futureecom/futureecom' => 
  array (
    'providers' => 
    array (
      0 => 'Futureecom\\Foundation\\FoundationServiceProvider',
      1 => 'Futureecom\\Utils\\AuthGuard\\AuthGuardServiceProvider',
      2 => 'Futureecom\\Utils\\SystemSettings\\SystemSettingsServiceProvider',
      3 => 'Futureecom\\Utils\\Tenancy\\TenancyServiceProvider',
      4 => 'Futureecom\\Utils\\Locale\\LocaleServiceProvider',
      5 => 'Futureecom\\Utils\\Time\\TimeServiceProvider',
      6 => 'Futureecom\\Utils\\DynamicAttributes\\DynamicAttributesServiceProvider',
      7 => 'Futureecom\\Foundation\\Plugins\\PluginsServiceProvider',
    ),
    'aliases' => 
    array (
      'Service' => 'Futureecom\\Foundation\\Support\\Facades\\Service',
      'Settings' => 'Futureecom\\Utils\\SystemSettings\\Facades\\Settings',
      'Plugins' => 'Futureecom\\Foundation\\Plugins\\Facades\\Plugins',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'laravel/vonage-notification-channel' => 
  array (
    'providers' => 
    array (
      0 => 'Illuminate\\Notifications\\VonageChannelServiceProvider',
    ),
    'aliases' => 
    array (
      'Vonage' => 'Illuminate\\Notifications\\Facades\\Vonage',
    ),
  ),
  'mongodb/laravel-mongodb' => 
  array (
    'providers' => 
    array (
      0 => 'MongoDB\\Laravel\\MongoDBServiceProvider',
      1 => 'MongoDB\\Laravel\\MongoDBQueueServiceProvider',
      2 => 'MongoDB\\Laravel\\MongoDBBusServiceProvider',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'nunomaduro/termwind' => 
  array (
    'providers' => 
    array (
      0 => 'Termwind\\Laravel\\TermwindServiceProvider',
    ),
  ),
  'prwnr/laravel-streamer' => 
  array (
    'aliases' => 
    array (
      'Streamer' => 'Prwnr\\Streamer\\Facades\\Streamer',
    ),
    'providers' => 
    array (
      0 => 'Prwnr\\Streamer\\StreamerProvider',
    ),
  ),
  'rinvex/countries' => 
  array (
    'providers' => 
    array (
      0 => 'Rinvex\\Country\\Providers\\CountryServiceProvider',
    ),
  ),
  'sebdesign/laravel-state-machine' => 
  array (
    'providers' => 
    array (
      0 => 'Sebdesign\\SM\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'StateMachine' => 'Sebdesign\\SM\\Facade',
    ),
  ),
  'spatie/laravel-ignition' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\LaravelIgnition\\IgnitionServiceProvider',
    ),
    'aliases' => 
    array (
      'Flare' => 'Spatie\\LaravelIgnition\\Facades\\Flare',
    ),
  ),
  'spatie/laravel-webhook-server' => 
  array (
    'providers' => 
    array (
      0 => 'Spatie\\WebhookServer\\WebhookServerServiceProvider',
    ),
  ),
  'toin0u/geocoder-laravel' => 
  array (
    'providers' => 
    array (
      0 => 'Geocoder\\Laravel\\Providers\\GeocoderService',
    ),
  ),
);