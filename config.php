<?php

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$config['db']['host']   = getenv('DB_HOST');
$config['db']['user']   = getenv('DB_USER');
$config['db']['pass']   = getenv('DB_PASS');
$config['db']['dbname'] = getenv('DB_NAME');
$config['db']['key'] = getenv('KEY');
