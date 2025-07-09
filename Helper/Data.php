<?php

declare(strict_types=1);

namespace JaroslawZielinski\OTPComponent\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    public static function escapeQuotes(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }
        return str_replace(['\''], ['&#39;'], $input);
    }

    /**
     * invert - shows sign of delta 0 means DateTime1 >= DateTime2, 1 means DateTime1 < DateTime2
     */
    public static function getTimeDiff(\DateTime $dateTime1, \DateTime $dateTime2): array
    {
        $diff = $dateTime1->diff($dateTime2);
        $invert = $diff->invert;
        $days = $diff->days;
        $hours = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;
        return [$invert, $days, $hours, $minutes, $seconds];
    }

    public static function getDateDiff(\DateTime $date1, \DateTime $date2): int
    {
        list ($invert, $days, $hours, $minutes, $seconds) = self::getTimeDiff($date2, $date1);
        $sign = $invert <= 0 ? 1 : -1;
        return $sign * $days;
    }
}
