<?php
function afgd5me_getClient() {
  $client = new Google_Client();
  $client->setApplicationName(afgd5me_APPLICATION_NAME);
  $client->setIncludeGrantedScopes(true);
  //$client->setScopes(SCOPES);
  $client->setAuthConfig(afgd5me_CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = afgd5me_CREDENTIALS_PATH;
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    $errstr='';
    $errstr .= "Credentials file does not exist";
    die( $errstr) ;
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }

  // FOR DEBUGGING ONLY
/*   $httpClient = new GuzzleHttp\Client([
    'proxy' => 'localhost:1338', // by default, Charles runs on localhost port 8888
    'verify' => false, // otherwise HTTPS requests will fail.
  ]);

  $client->setHttpClient($httpClient); */
  return $client;
}
