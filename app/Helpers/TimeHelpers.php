<?php

function secondsToHms($time)
{
    if (!is_numeric($time)) $time = intval($time);

    $result = '';
    $seconds = $time < 60;

    $units = [
        'hour'   => 3600,
        'minute' => 60,
    ];

    if ($seconds) {
        $units['second'] = 1;
    }

    foreach ($units as $unit => $unitSeconds) {
        if ($time >= $unitSeconds) {
            $diff = floor($time / $unitSeconds);
            $result = $result . trans_choice('app.global_time_value_part_' . $unit, $diff, ['value' => $diff]) . ', ';
            $time -= $unitSeconds * $diff;
        }
    }

    $result = rtrim($result, ', ');

    if (empty($result)) {
        if ($seconds) {
            $result = trans_choice('app.global_time_value_part_second', 0, ['value' => 0]);
        } else {
            $result = trans_choice('app.global_time_value_part_minute', 0, ['value' => 0]);
        }
    }

    return $result;
}

function secondsToHmsAlternative($time)
{
    if (!is_numeric($time)) $time = intval($time);

    $result = '';


    $units = [
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1
    ];

    foreach ($units as $unit => $unitSeconds) {
        $diff = floor($time / $unitSeconds);
        $result = $result . str_pad($diff, 2, '0', STR_PAD_LEFT) . ':';
        $time -= $unitSeconds * $diff;
    }

    $result = rtrim($result, ':');

    if (empty($result)) {
        $result = '00:00:00';
    }

    return $result;
}

function secondsToDecimal($time)
{
    if (!is_numeric($time)) $time = intval($time);

    $value = '';
    $value .= floor($time / 3600);

    if (($time % 3600) != 0) {
        $value .= '.';
        $value .= ceil(100 * ($time % 3600) / 3600);
    }

    $value = (float) $value;

    $result = trans_choice('app.global_time_value_part_hour_decimal', $value, ['value' => $value]);
    return $result;
}

function filterReportsArray($array)
{
    return array_filter($array, function ($x) {
        return is_array($x);
    });
}
