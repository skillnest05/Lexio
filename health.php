<?php
// Lightweight health-check endpoint — no DB, no session, no includes.
// Railway probes this to verify the container is alive.
http_response_code(200);
header('Content-Type: text/plain');
echo 'OK';
