<?php
// Footer Script
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$date_time_cet = new DateTime("now", new DateTimeZone("CET"));

// Detect browser
function getBrowser($user_agent) {
    if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
        return 'Internet Explorer';
    } elseif (strpos($user_agent, 'Edge') !== false) {
        return 'Microsoft Edge';
    } elseif (strpos($user_agent, 'Firefox') !== false) {
        return 'Mozilla Firefox';
    } elseif (strpos($user_agent, 'Chrome') !== false) {
        return 'Google Chrome';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        return 'Apple Safari';
    } elseif (strpos($user_agent, 'Opera') !== false || strpos($user_agent, 'OPR') !== false) {
        return 'Opera';
    } else {
        return 'Unknown Browser';
    }
}

// Detect operating system
function getOS($user_agent) {
    $os_array = [
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.1/i' => 'Windows XP',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    ];

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            return $value;
        }
    }
    return 'Unknown OS';
}

$browser = getBrowser($user_agent);
$os = getOS($user_agent);
?>

<div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #ddd; margin-top: 20px;">
    <p style="margin: 5px; font-size: 14px;">
        <strong>Your IP:</strong> <?php echo $ip_address; ?>
    </p>
    <p style="margin: 5px; font-size: 14px;">
        <strong>Operating System:</strong> <?php echo $os; ?>
    </p>
    <p style="margin: 5px; font-size: 14px;">
        <strong>Browser:</strong> <?php echo $browser; ?>
    </p>
    <p style="margin: 5px; font-size: 14px;">
        <strong>Date & Time (CET):</strong> <?php echo $date_time_cet->format('Y-m-d H:i:s'); ?>
    </p>
</div>
