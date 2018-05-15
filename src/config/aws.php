<?php
use Aws\Laravel\AwsServiceProvider;
$host = env('CELLAR_ADDON_HOST');
if(strpos($host,'http') === false){
    $host='http://'.$host;
}
return [

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */

    'region' => '',
    'endpoint' => $host,
    'signature_version'=> 'v2',
    'version' => 'latest',
    'credentials' => [
        'key'    => env('CELLAR_ADDON_KEY_ID'),
        'secret' => env('CELLAR_ADDON_KEY_SECRET'),
    ]
];
