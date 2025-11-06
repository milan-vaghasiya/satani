<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
|| Android Firebase Push Notification Configurations
|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
 */

/*
|--------------------------------------------------------------------------
| Firebase API Key
|--------------------------------------------------------------------------
|
| The secret key for Firebase API
|
 */
//Nativebit
$config['key'] = 'AAAAoe6z20w:APA91bHiHMGAS4P-FGsZa_uvViAbAVcG_jCcx1RRHh2VLhpqAKx0Gcntw7v-Tjvtc7GvD4EZ_kYcfjpFEYm2OohMl5FB_xJ0CBXv4gN5S5mE9J4NZYDRO4SFPtvpbdcOzbTSRhYHtrNN';

/*
|--------------------------------------------------------------------------
| Firebase Cloud Messaging API URL
|--------------------------------------------------------------------------
|
| The URL for Firebase Cloud Messafing
|
 */

$config['fcm_url'] = 'https://fcm.googleapis.com/fcm/send';
