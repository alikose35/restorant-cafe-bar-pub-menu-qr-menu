<?php
declare(strict_types=1);

namespace App\Core;

final class DiscountEngine
{
    /**
     * @param array<string, mixed> $discount
     */
    public static function isActiveNow(array $discount, ?\DateTimeImmutable $now = null): bool
    {
        if ((int) ($discount['is_active'] ?? 0) !== 1) {
            return false;
        }

        $now = $now ?? new \DateTimeImmutable('now');
        $ruleType = (string) ($discount['rule_type'] ?? 'always');

        if ($ruleType === 'always') {
            return true;
        }

        if ($ruleType === 'date_range') {
            return self::checkDateRange($discount, $now);
        }

        if ($ruleType === 'weekly_time') {
            return self::checkWeeklyWindow($discount, $now);
        }

        return false;
    }

    /**
     * @param array<string, mixed> $discount
     */
    private static function checkDateRange(array $discount, \DateTimeImmutable $now): bool
    {
        $startRaw = trim((string) ($discount['starts_at'] ?? ''));
        $endRaw = trim((string) ($discount['ends_at'] ?? ''));

        if ($startRaw !== '') {
            $start = new \DateTimeImmutable($startRaw);
            if ($now < $start) {
                return false;
            }
        }

        if ($endRaw !== '') {
            $end = new \DateTimeImmutable($endRaw);
            if ($now > $end) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $discount
     */
    private static function checkWeeklyWindow(array $discount, \DateTimeImmutable $now): bool
    {
        if (!self::checkDateRange($discount, $now)) {
            return false;
        }

        $weekdaysCsv = trim((string) ($discount['weekdays_csv'] ?? ''));
        $startTimeRaw = trim((string) ($discount['start_time'] ?? ''));
        $endTimeRaw = trim((string) ($discount['end_time'] ?? ''));
        if ($weekdaysCsv === '' || $startTimeRaw === '' || $endTimeRaw === '') {
            return false;
        }

        $weekdays = array_values(array_unique(array_filter(
            array_map('intval', explode(',', $weekdaysCsv)),
            static fn (int $day): bool => $day >= 1 && $day <= 7
        )));
        if (empty($weekdays)) {
            return false;
        }

        $currentWeekday = (int) $now->format('N');
        $currentMinutes = ((int) $now->format('H')) * 60 + (int) $now->format('i');
        [$startMinutes, $endMinutes] = self::timeRangeMinutes($startTimeRaw, $endTimeRaw);

        if ($startMinutes <= $endMinutes) {
            if (!in_array($currentWeekday, $weekdays, true)) {
                return false;
            }
            return $currentMinutes >= $startMinutes && $currentMinutes <= $endMinutes;
        }

        if (in_array($currentWeekday, $weekdays, true) && $currentMinutes >= $startMinutes) {
            return true;
        }

        $prevDay = $currentWeekday === 1 ? 7 : $currentWeekday - 1;
        if (in_array($prevDay, $weekdays, true) && $currentMinutes <= $endMinutes) {
            return true;
        }

        return false;
    }

    /**
     * @return array{0:int,1:int}
     */
    private static function timeRangeMinutes(string $startTimeRaw, string $endTimeRaw): array
    {
        [$sh, $sm] = array_pad(array_map('intval', explode(':', $startTimeRaw)), 2, 0);
        [$eh, $em] = array_pad(array_map('intval', explode(':', $endTimeRaw)), 2, 0);
        return [
            max(0, min(23, $sh)) * 60 + max(0, min(59, $sm)),
            max(0, min(23, $eh)) * 60 + max(0, min(59, $em)),
        ];
    }
}
