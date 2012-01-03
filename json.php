<?php

/**
 * This code is an example of how to track live locations with google maps. For
 * a live example check http://toshl.com/live/
 *
 * For this code to work you must download GeoLiteCity.dat (binary) database from
 * http://www.maxmind.com/app/geolitecity because it is around 30MB and gets
 * updated every month. For any licensing restrictions please see
 * http://geolite.maxmind.com/download/geoip/database/LICENSE.txt
 *
 * @author Miha Hribar
 */

// need a little bit more memory, set to a value you know will work and not cause problems
ini_set('memory_limit','512M');

// include geoip stuff
require_once 'geoipcity.php';
require_once 'geoipregionvars.php';

session_start();

$ips = array(
    '166.147.78.230',
    '95.144.191.105',
    '193.64.22.143',
    '67.250.42.185',
    '166.147.78.230',
    '90.177.19.223',
    '82.39.31.117',
    '59.189.18.60',
    '186.204.149.18',
    '94.192.52.229',
    '201.29.232.80',
    '200.95.163.84',
    '186.204.149.18',
    '200.95.163.84',
    '97.121.4.35',
    '70.68.58.80',
    '166.248.1.40',
    '74.198.150.194',
    '99.113.130.93',
    '72.136.226.163',
    '67.250.42.185',
    '174.254.86.120',
    '76.113.199.246',
    '66.87.101.148',
    '186.204.149.18',
    '76.95.41.164',
    '69.38.252.83',
);

// open city database
$gi = geoip_open('GeoLiteCity.dat', GEOIP_STANDARD);

// get lat & lon for ips
$list = array();
foreach ($ips as $ip)
{
    $record = geoip_record_by_addr($gi, $ip);
    if ($record)
    {
        $list[] = array(
            $record->latitude,
            $record->longitude,
            $record->city.' '.$record->postal_code,
        );
    }
}

// return just next location in line
if (!array_key_exists('i', $_SESSION))
{
    $_SESSION['i'] = 0;
}

$return = array();
if (isset($list[$_SESSION['i']]))
{
    $return = array($list[$_SESSION['i']]);
    $_SESSION['i'] = $_SESSION['i']+1;
}
echo json_encode($return);

if (array_key_exists('reset', $_GET))
{
    unset($_SESSION['i']);
}