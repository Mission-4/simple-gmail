<?php

return [
    'scope' => implode(' ', [Google_Service_Gmail::GMAIL_READONLY, Google_Service_Gmail::GMAIL_SEND]),
    'project_id' => env('GOOGLE_PROJECT_ID'),
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_REDIRECT_URI', '/'),
];
