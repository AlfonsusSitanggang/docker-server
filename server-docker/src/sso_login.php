<?php
// Konfigurasi Keycloak
$keycloakUrl = 'http://localhost:8081'; // URL Keycloak
$realm = 'smartguard';                  // Realm 
$clientId = 'web-app';                  // Client ID 
$redirectUri = 'http://localhost:8080/index.php'; // Balik ke  login

// Buat URL OIDC (OpenID Connect) secara manual
$authUrl = "$keycloakUrl/realms/$realm/protocol/openid-connect/auth" .
    "?client_id=$clientId" .
    "&response_type=code" .
    "&redirect_uri=" . urlencode($redirectUri) .
    "&scope=openid";

// Lempar User ke Keycloak
header("Location: $authUrl");
exit();
?>