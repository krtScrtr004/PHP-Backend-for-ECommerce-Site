<?php

// Set the response content type to JSON
header('Content-Type: application/json');

// Allow requests from any origin (CORS)
header('Access-Control-Allow-Origin: *');

// Allow specific HTTP methods for CORS
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Prevent MIME type sniffing by browsers
header('X-Content-Type-Options: nosniff');

// Prevent the page from being displayed in a frame (clickjacking protection)
header('X-Frame-Options: DENY');
