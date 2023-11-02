<?php

return [

    /**
     * Basic Auth
     */
    'basicauth_user' => env('BASICAUTH_USER', 'alpha'),
    'basicauth_password' => env('BASICAUTH_PASSWORD', 'beta'),
    'basicauth_to_at' => env('BASICAUTH_TO_AT', false),

    /**
     * IP Restriction
     */
    'ip_restriction_allow_ips' => env('IP_RESTRICTION_ALLOW_IPS', ''),
    'ip_restriction_to_at' => env('IP_RESTRICTION_TO_AT', ''),
];
