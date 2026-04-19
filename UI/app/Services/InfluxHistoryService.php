<?php

namespace App\Services;

use Carbon\Carbon;
use InfluxDB2\Client;
use Throwable;

class InfluxHistoryService
{
    private const MEASUREMENT = 'device_status';

    private const FIELD_TEMP = 'temp';

    /** @var array<int, string> niedziela = 0 */
    private const PL_WEEKDAY_SHORT = ['Nd', 'Pn', 'Wt', 'Śr', 'Czw', 'Pt', 'So'];

    /**
     * @return array{
     *     historyPoints: list<array{label: string, temp: float}>,
     *     historyPoints24h: list<array{label: string, temp: float}>,
     *     historyPoints7d: list<array{label: string, temp: float}>,
     *     historyPoints30d: list<array{label: string, temp: float}>,
     *     lastUpdatedLabel: ?string
     * }
     */
    public function forDevice(string $deviceUid, int $userId): array
    {
        $empty = [
            'historyPoints' => [],
            'historyPoints24h' => [],
            'historyPoints7d' => [],
            'historyPoints30d' => [],
            'lastUpdatedLabel' => null,
        ];

        $url = env('INFLUXDB_URL');
        $token = env('INFLUXDB_TOKEN');
        $bucket = env('INFLUXDB_BUCKET');
        $org = env('INFLUXDB_ORG');

        if (! is_string($url) || $url === '' || ! is_string($token) || $token === ''
            || ! is_string($bucket) || $bucket === '' || ! is_string($org) || $org === '') {
            return $empty;
        }

        try {
            $client = new Client([
                'url' => $url,
                'token' => $token,
                'bucket' => $bucket,
                'org' => $org,
            ]);
            $queryApi = $client->createQueryApi();
        } catch (Throwable) {
            return $empty;
        }

        $userTag = 'user_'.$userId;
        $deviceEsc = self::fluxStringLiteral($deviceUid);
        $userEsc = self::fluxStringLiteral($userTag);
        $bucketEsc = self::fluxStringLiteral($bucket);

        $lastUpdatedLabel = $this->queryLastTempTimeLabel($queryApi, $bucketEsc, $userEsc, $deviceEsc);

        $raw24 = $this->runAggregatedQuery(
            $queryApi,
            $bucketEsc,
            $userEsc,
            $deviceEsc,
            '-24h',
            '1h'
        );
        $raw7 = $this->runAggregatedQuery(
            $queryApi,
            $bucketEsc,
            $userEsc,
            $deviceEsc,
            '-7d',
            '1d'
        );
        $raw30 = $this->runAggregatedQuery(
            $queryApi,
            $bucketEsc,
            $userEsc,
            $deviceEsc,
            '-30d',
            '1d'
        );

        $tz = config('app.timezone', 'UTC');
        $points24 = $this->mapToChartPoints($raw24, fn (Carbon $dt): string => $dt->timezone($tz)->format('G:i'));
        $points7 = $this->mapToChartPoints($raw7, function (Carbon $dt) use ($tz): string {
            $local = $dt->timezone($tz);

            return self::PL_WEEKDAY_SHORT[(int) $local->format('w')];
        });
        $points30 = $this->mapToChartPoints($raw30, fn (Carbon $dt): string => $dt->timezone($tz)->format('j.n'));

        $preview = count($points24) > 6 ? array_slice($points24, -6) : $points24;

        return [
            'historyPoints' => $preview,
            'historyPoints24h' => $points24,
            'historyPoints7d' => $points7,
            'historyPoints30d' => $points30,
            'lastUpdatedLabel' => $lastUpdatedLabel,
        ];
    }

    private function queryLastTempTimeLabel($queryApi, string $bucketEsc, string $userEsc, string $deviceEsc): ?string
    {
        $flux = sprintf(
            'from(bucket: "%s")
            |> range(start: -48h)
            |> filter(fn: (r) => r["_measurement"] == "%s")
            |> filter(fn: (r) => r["user_id"] == "%s")
            |> filter(fn: (r) => r["device_id"] == "%s")
            |> filter(fn: (r) => r["_field"] == "%s")
            |> last()
            |> timeShift(duration: 2h)',
            $bucketEsc,
            self::MEASUREMENT,
            $userEsc,
            $deviceEsc,
            self::FIELD_TEMP
        );

        try {
            $tables = $queryApi->query($flux);
        } catch (Throwable) {
            return null;
        }

        $latest = null;
        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                if ($record->getField() !== self::FIELD_TEMP) {
                    continue;
                }
                $c = self::carbonFromInfluxTime($record->getTime());
                if ($c === null) {
                    continue;
                }
                if ($latest === null || $c->greaterThan($latest)) {
                    $latest = $c;
                }
            }
        }

        if ($latest === null) {
            return null;
        }

        return $latest->timezone(config('app.timezone', 'UTC'))->format('H:i d.m.Y');
    }

    /**
     * @return list<array{time: string, value: float}>
     */
    private function runAggregatedQuery(
        $queryApi,
        string $bucketEsc,
        string $userEsc,
        string $deviceEsc,
        string $range,
        string $every
    ): array {
        if (! in_array($range, ['-24h', '-7d', '-30d'], true) || ! in_array($every, ['1h', '1d'], true)) {
            return [];
        }

        $flux = sprintf(
            'import "timezone"
            option location = location.load(name: "Europe/Warsaw")
            from(bucket: "%s")
            |> range(start: %s)
            |> filter(fn: (r) => r["_measurement"] == "%s")
            |> filter(fn: (r) => r["user_id"] == "%s")
            |> filter(fn: (r) => r["device_id"] == "%s")
            |> filter(fn: (r) => r["_field"] == "%s")
            |> aggregateWindow(every: %s, fn: mean, createEmpty: false)
            |> sort(columns: ["_time"])',
            $bucketEsc,
            $range,
            self::MEASUREMENT,
            $userEsc,
            $deviceEsc,
            self::FIELD_TEMP,
            $every
        );

        try {
            $tables = $queryApi->query($flux);
        } catch (Throwable) {
            return [];
        }

        $rows = [];
        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                if ($record->getField() !== self::FIELD_TEMP) {
                    continue;
                }
                $value = $record->getValue();
                if ($value === null) {
                    continue;
                }
                $timeCarbon = self::carbonFromInfluxTime($record->getTime());
                if ($timeCarbon === null) {
                    continue;
                }
                $rows[] = ['time' => $timeCarbon->format('c'), 'value' => round((float) $value, 1)];
            }
        }

        usort($rows, fn (array $a, array $b): int => strcmp($a['time'], $b['time']));

        return $rows;
    }

    /**
     * @param  list<array{time: string, value: float}>  $rows
     * @return list<array{label: string, temp: float}>
     */
    private function mapToChartPoints(array $rows, callable $labelFn): array
    {
        $out = [];
        foreach ($rows as $row) {
            try {
                $dt = Carbon::parse($row['time']);
            } catch (Throwable) {
                continue;
            }
            $out[] = ['label' => $labelFn($dt), 'temp' => $row['value']];
        }

        return $out;
    }

    private static function fluxStringLiteral(string $value): string
    {
        return str_replace(["\n", "\r", '\\', '"'], ['\\n', '\\r', '\\\\', '\"'], $value);
    }

    private static function carbonFromInfluxTime(mixed $time): ?Carbon
    {
        if ($time instanceof \DateTimeInterface) {
            try {
                return Carbon::parse($time);
            } catch (Throwable) {
                return null;
            }
        }
        if (is_string($time) && $time !== '') {
            try {
                return Carbon::parse($time);
            } catch (Throwable) {
                return null;
            }
        }

        return null;
    }
}
