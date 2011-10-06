<?php
function valid_email_address($mail) {
$user = '[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+';
$domain = '(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+';
$ipv4 = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
$ipv6 = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';
return preg_match("/^$user@($domain|(\[($ipv4|$ipv6)\]))$/", $mail);
}