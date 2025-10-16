<?php

/**
 * ============================================================================
 * AVAILABILITY LOGIC — README
 * ============================================================================
 *
 * Core fields (stored on User via ACF):
 * -------------------------------------
 * - availability_status: "available" | "booked" | "penciled"
 * - available_from: (Y-m-d) optional, required if penciled
 * - available_until: (Y-m-d) optional
 *
 * Behavior rules:
 * ---------------
 * 1. When "availability_status" = booked
 *    → "available_from" is cleared automatically.
 *    → "available_until" is optional (indicates when they'll be available again).
 *    → The user is considered BOOKED until the "until" date (if set).
 *
 * 2. When "availability_status" = available
 *    → "available_from" and "available_until" are optional.
 *      - No dates = fully available.
 *      - Only "available_from" in future = "Available from {date}".
 *      - Only "available_until" in future = "Available until {date}".
 *      - If "available_until" is in the past → "Booked" (availability window expired).
 *
 * 3. When "availability_status" = penciled
 *    → "available_from" is required; "available_until" optional.
 *      - T < from → "Penciled from {date}".
 *      - from ≤ T ≤ until (or no until) → "Penciled until {until}" / "Penciled".
 *      - T > until → "Booked" (pencil window expired).
 *
 * Notes:
 * ------
 * - "from" and "until" are validated so that from ≤ until (otherwise "until" is cleared).
 * - When the user manually changes their status (front-end or admin),
 *   fields are normalized automatically:
 *     * status = booked → clears from, keeps until
 *     * status = penciled → ensures from exists
 * - The logic for badge display is centralized in site_profile_availability_badge().
 * - site_next_available_ts_from_user() computes a timestamp for sorting by soonest availability.
 *
 * Examples:
 * ----------
 *  status=available | from=—        | until=—         → "Available"
 *  status=available | from=2025-11-10 | until=—       → "Available from 2025-11-10"
 *  status=available | from=—        | until=2025-11-30 → "Available until 2025-11-30"
 *  status=booked    | —             | —               → "Booked"
 *  status=booked    | —             | until=2025-11-30 → "Booked until 2025-11-30"
 *  status=penciled  | from=2025-11-12 | until=—       → "Penciled" (open-ended)
 *  status=penciled  | from=2025-11-05 | until=2025-11-20 → "Penciled until 2025-11-20"
 *  status=penciled  | from=2025-11-05 | until=2025-11-10 | (today > 11-10) → "Booked"
 *
 * ============================================================================
 */

if (!defined('ABSPATH')) exit;

/**
 * Normalize availability fields after ANY save to the user.
 * - booked  => clears from, keeps until (optional "booked until" date)
 * - penciled => requires from (if missing, clears until)
 * - available => optional from/until
 * - also enforces from <= until (if both set)
 */
function site_normalize_availability_for_user(int $user_id): void
{
  $status = get_user_meta($user_id, 'availability_status', true) ?: 'available';
  $from   = (string) get_user_meta($user_id, 'available_from', true);
  $until  = (string) get_user_meta($user_id, 'available_until', true);

  $is_date = static function ($d) {
    return is_string($d) && $d !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d);
  };

  if ($from && !$is_date($from))   $from = '';
  if ($until && !$is_date($until)) $until = '';

  if ($status === 'booked') {
    // Clear "from" but keep "until" to allow "booked until X date"
    $from = '';
  } elseif ($status === 'penciled') {
    if (!$from) $until = ''; // require FROM; if absent, drop UNTIL
  } else {
    // available: keep optional from/until
  }

  if ($from && $until && strcmp($from, $until) > 0) {
    // invalid order → keep FROM, drop UNTIL
    $until = '';
  }

  update_user_meta($user_id, 'available_from',  $from);
  update_user_meta($user_id, 'available_until', $until);
}

/**
 * Compute "soonest available" timestamp for sorting lists.
 * Matches the agreed display semantics.
 */
function site_next_available_ts_from_user(int $user_id): int
{
  $T      = current_time('Y-m-d');
  $status = get_user_meta($user_id, 'availability_status', true) ?: 'available';
  $from   = (string) get_user_meta($user_id, 'available_from', true);
  $until  = (string) get_user_meta($user_id, 'available_until', true);

  if ($status === 'booked') {
    // If there's a "booked until" date, they'll be available after that
    if ($until && $T <= $until) {
      // Calculate availability as day after the "until" date
      return strtotime($until . ' 00:00:00') + DAY_IN_SECONDS;
    }
    // No until date means indefinitely booked
    return PHP_INT_MAX;
  }

  if ($status === 'available') {
    if ($from && $T < $from) return strtotime($from . ' 00:00:00');
    if ($until && $T > $until) return PHP_INT_MAX; // window ended → you show "Booked"
    return strtotime($T . ' 00:00:00');
  }

  // penciled
  if ($from && $T < $from) return strtotime($from . ' 00:00:00');
  if (!$until || $T <= $until) return PHP_INT_MAX; // penciled overlaps / open-ended
  // if pencil ended and you display "Booked", treat as unknown
  return PHP_INT_MAX;
}

/**
 * Human badge for a Profile post ID (date-sensitive).
 * Use on single-profile.php.
 */
function site_profile_availability_badge(int $post_id, ?string $today = null): string
{
  $today = $today ?: current_time('Y-m-d');

  $status = get_post_meta($post_id, 'availability_status', true) ?: 'available';
  $from   = get_post_meta($post_id, 'available_from', true) ?: '';
  $until  = get_post_meta($post_id, 'available_until', true) ?: '';

  if ($status === 'booked') {
    // Show "Booked until X" if there's an until date, otherwise just "Booked"
    return $until ? 'Booked until ' . esc_html($until) : 'Booked';
  }

  if ($status === 'available') {
    if ($from && $today < $from)        return 'Available from ' . esc_html($from);
    if ($until && $today <= $until)     return 'Available until ' . esc_html($until);
    if ($until && $today >  $until)     return 'Booked';
    return 'Available';
  }

  // penciled
  if ($from && $today < $from)          return 'Penciled from ' . esc_html($from);
  if ($from && (!$until || $today <= $until)) {
    return $until ? 'Penciled until ' . esc_html($until) : 'Penciled';
  }
  if ($until && $today > $until)        return 'Booked';
  return 'Penciled';
}


// Require FROM when status = penciled
add_filter('acf/validate_value/name=available_from', function ($valid, $value) {
  if (!$valid) return $valid;
  $status = $_POST['acf']['availability_status'] ?? $_POST['availability_status'] ?? null;
  if ($status === 'penciled' && empty($value)) {
    return __('“Available from” is required when status is Penciled.', 'textdomain');
  }
  return $valid;
}, 10, 2);

// Ensure FROM <= UNTIL when both set
add_filter('acf/validate_value/name=available_until', function ($valid, $value) {
  if (!$valid) return $valid;
  $from = $_POST['acf']['available_from'] ?? null;
  if ($from && $value && strcmp($from, $value) > 0) {
    return __('“Available until” must be the same day or after “Available from”.', 'textdomain');
  }
  return $valid;
}, 10, 2);
