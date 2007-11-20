#!/bin/bash
htpasswd2 -bc cache/security/.htpasswd etoa ""
chown etoa:apache cache/security/.htpasswd
chmod g+w cache/security/.htpasswd
echo "Das Admin-User wurde auf 'etoa' und das Passwort auf '' gesetzt!"
