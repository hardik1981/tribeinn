<?php
declare(strict_types=1);

final class AvailabilityDates {
    public static function date(string $value): bool {
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $value)) return false;
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return (bool) $date && $date->format('Y-m-d') === $value && $value >= '1000-01-01';
    }
    public static function validate(string $start, string $end, string $today): ?string {
        if (!self::date($start) || !self::date($end)) return 'Choose valid check-in and check-out dates.';
        if ($start < $today) return 'Check-in cannot be in the past.';
        if ($end <= $start) return 'Check-out needs to be after check-in.';
        return null;
    }
    public static function overlaps(string $start, string $end, array $block): bool {
        return $start < $block['end'] && $end > $block['start'];
    }
}

interface AvailabilityProvider {
    /** Return occupied [start,end) ranges, not reasons or sources. */
    public function ranges(string $unit, string $today): array;
}

final class AvailabilityService {
    public function __construct(private array $providers) {}
    public function ranges(string $unit, string $today): array {
        $ranges = [];
        foreach ($this->providers as $provider) {
            foreach ($provider->ranges($unit, $today) as $range) {
                if (!AvailabilityDates::date($range['start']) || !AvailabilityDates::date($range['end']) || $range['end'] <= $range['start']) throw new RuntimeException('Invalid availability range');
                if ($range['end'] > $today) $ranges[] = ['start'=>$range['start'], 'end'=>$range['end']];
            }
        }
        usort($ranges, fn($a, $b) => strcmp($a['start'], $b['start']));
        $merged = [];
        foreach ($ranges as $range) {
            $last = count($merged) - 1;
            if ($last >= 0 && $range['start'] <= $merged[$last]['end']) $merged[$last]['end'] = max($merged[$last]['end'], $range['end']);
            else $merged[] = $range;
        }
        return $merged;
    }
    public function check(string $unit, string $start, string $end, string $today): ?string {
        if ($error = AvailabilityDates::validate($start, $end, $today)) return $error;
        foreach ($this->ranges($unit, $today) as $range) {
            if (AvailabilityDates::overlaps($start, $end, $range)) return 'These dates include unavailable nights. Please choose another stay.';
        }
        return null;
    }
}

final class MysqlAvailabilityRepository implements AvailabilityProvider {
    public function __construct(private PDO $db) {}
    public function unit(string $slug): array {
        $query = $this->db->prepare('SELECT id, slug, name FROM units WHERE slug = ? AND active = 1');
        $query->execute([$slug]);
        return $query->fetch() ?: throw new OutOfBoundsException('Unknown unit');
    }
    public function ranges(string $unit, string $today): array {
        $id = $this->unit($unit)['id'];
        $query = $this->db->prepare('SELECT start_date AS start, end_date AS end FROM availability_blocks WHERE unit_id = ? AND end_date > ? ORDER BY start_date');
        $query->execute([$id, $today]);
        return $query->fetchAll();
    }
    public function manualBlocks(string $unit, string $today): array {
        $id = $this->unit($unit)['id'];
        $query = $this->db->prepare("SELECT id, start_date, end_date, reason FROM availability_blocks WHERE unit_id = ? AND source = 'manual' AND end_date > ? ORDER BY start_date, id");
        $query->execute([$id, $today]);
        return $query->fetchAll();
    }
    public function save(string $unit, ?int $id, string $start, string $end, string $reason, string $today): void {
        // An ongoing block may be edited without losing its historical start date.
        if ($error = AvailabilityDates::validate($start, $end, $id ? '1000-01-01' : $today)) throw new InvalidArgumentException($error);
        if ($end <= $today) throw new InvalidArgumentException('The end date must be in the future.');
        if (!mb_check_encoding($reason, 'UTF-8') || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $reason)) throw new InvalidArgumentException('Please use plain text for the private reason.');
        if (mb_strlen($reason) > 500) throw new InvalidArgumentException('Keep the private reason within 500 characters.');
        $unitId = $this->unit($unit)['id'];
        if ($id) {
            $this->manualBlock($unitId, $id);
            $query = $this->db->prepare("UPDATE availability_blocks SET start_date = ?, end_date = ?, reason = ? WHERE id = ? AND unit_id = ? AND source = 'manual'");
            $query->execute([$start, $end, $reason, $id, $unitId]);
        } else {
            $query = $this->db->prepare("INSERT INTO availability_blocks (unit_id, start_date, end_date, reason, source) VALUES (?, ?, ?, ?, 'manual')");
            $query->execute([$unitId, $start, $end, $reason]);
        }
    }
    public function remove(string $unit, int $id): void {
        $unitId = $this->unit($unit)['id'];
        $this->manualBlock($unitId, $id);
        $query = $this->db->prepare("DELETE FROM availability_blocks WHERE id = ? AND unit_id = ? AND source = 'manual'");
        $query->execute([$id, $unitId]);
        if (!$query->rowCount()) throw new InvalidArgumentException('This manual block no longer exists. Reload the calendar.');
    }
    private function manualBlock(int|string $unitId, int $id): void {
        $query = $this->db->prepare("SELECT id FROM availability_blocks WHERE id = ? AND unit_id = ? AND source = 'manual'");
        $query->execute([$id, $unitId]);
        if (!$query->fetchColumn()) throw new InvalidArgumentException('This manual block no longer exists. Reload the calendar.');
    }
}

function availabilityConfig(): array { return require __DIR__ . '/../../config/availability.php'; }
function availabilityToday(array $config): string { return (new DateTimeImmutable('today', new DateTimeZone($config['timezone'])))->format('Y-m-d'); }
function availabilityRepository(array $config): MysqlAvailabilityRepository {
    if (!str_starts_with($config['dsn'], 'mysql:')) throw new RuntimeException('Availability database not configured');
    return new MysqlAvailabilityRepository(new PDO($config['dsn'], $config['db_user'], $config['db_password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false, PDO::ATTR_TIMEOUT=>3]));
}
function availabilityService(array $config): AvailabilityService {
    // Future imported ranges/providers join here; the public interface stays unchanged.
    return new AvailabilityService([availabilityRepository($config)]);
}
/** Outages allow an explicitly unverified enquiry; an unknown unit does not. */
function availabilityEnquiryDecision(array $config, string $start, string $end, ?AvailabilityService $service = null): array {
    try {
        return ['error'=>($service ?? availabilityService($config))->check($config['unit'], $start, $end, availabilityToday($config)), 'verified'=>true];
    } catch (OutOfBoundsException $error) {
        return ['error'=>'This stay is not currently accepting date requests. Please contact TribeInn.', 'verified'=>false];
    } catch (Throwable $error) {
        return ['error'=>null, 'verified'=>false];
    }
}
function availabilityPublic(array $config, array $query, ?AvailabilityService $service = null): array {
    $unit = $query['unit'] ?? $config['unit'];
    if (!is_string($unit) || !preg_match('/^[a-z0-9-]{1,100}$/D', $unit)) return [400, ['message'=>'Invalid unit.']];
    $today = availabilityToday($config);
    $start = $query['start'] ?? null; $end = $query['end'] ?? null;
    if ($start !== null || $end !== null) {
        if (!is_string($start) || !is_string($end) || ($error = AvailabilityDates::validate($start, $end, $today))) return [422, ['message'=>$error ?? 'Choose valid dates.']];
    }
    try {
        $ranges = ($service ?? availabilityService($config))->ranges($unit, $today);
        $result = ['unit'=>$unit, 'today'=>$today, 'unavailable'=>$ranges];
        if ($start !== null) {
            $result['valid'] = !array_filter($ranges, fn($range) => AvailabilityDates::overlaps($start, $end, $range));
        }
        return [200, $result];
    } catch (OutOfBoundsException $error) {
        return [404, ['message'=>'Unit not found.']];
    } catch (Throwable $error) {
        return [503, ['message'=>"Availability couldn't be loaded right now. You can still send us your dates and we'll confirm them with you."]];
    }
}
