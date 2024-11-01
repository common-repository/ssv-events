<?php
if (!defined('ABSPATH')) {
    exit;
}

#region Functions that should be in PHP
function mp_ssv_replace_at_pos($haystack, $needle, $replacement, $position)
{
    return substr_replace($haystack, $replacement, $position, strlen($needle));
}

function mp_ssv_starts_with($haystack, $needle)
{
    return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function mp_ssv_ends_with($haystack, $needle)
{
    return $needle === '' || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}
#endregion
