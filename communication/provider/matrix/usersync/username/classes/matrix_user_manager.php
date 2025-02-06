<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace matrixusersync_username;

use communication_matrix\matrix_user_manager_base;

/**
 * class matrix_user_manager to handle specific actions.
 *
 * @package    matrixusersync_username
 * @copyright  2025 André Menrath <andre.menrath@posteo.de>, University of Graz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix_user_manager extends matrix_user_manager_base {
    /**
     * Prefix for Matrix usernames when they are detected as numeric.
     *
     * @var string
     */
    private const MATRIX_USER_PREFIX = 'user';

    /**
     * GetsMatrix User ID from moodle.
     *
     * @param int $userid Moodle user id
     * @return string|null
     */
    public static function get_matrixid_from_moodle(
        int $userid,
    ): ?string {
        self::load_requirements();
        $field = profile_user_record($userid);
        $matrixprofilefield = get_config('communication_matrix', 'matrixuserid_field');

        if ($matrixprofilefield === false) {
            return null;
        }

        return $field->{$matrixprofilefield} ?? null;
    }

    /**
     * Get a qualified Matrix User ID based on a Moodle username.
     *
     * @param string $username The moodle username to turn into a Matrix username
     * @return string
     */
    public static function get_formatted_matrix_userid(
        string $username,
    ): string {
        $username = preg_replace('/[@#$%^&*()+{}|<>?!,]/i', '.', $username);
        $username = ltrim(rtrim($username, '.'), '.');

        // Matrix/Synapse servers will not allow numeric usernames.
        if (is_numeric($username)) {
            $username = self::MATRIX_USER_PREFIX . $username;
        }

        $homeserver = self::get_formatted_matrix_home_server();

        return "@{$username}:{$homeserver}";
    }

    /**
     * Sets home server for user matrix id
     *
     * @return string
     */
    private static function get_formatted_matrix_home_server(): string {
        $homeserver = get_config('communication_matrix', 'matrixhomeserverurl');
        if ($homeserver === false) {
            throw new \moodle_exception('Unknown matrix homeserver url');
        }

        $homeserver = parse_url($homeserver)['host'];

        if (str_starts_with($homeserver, 'www.')) {
            $homeserver = str_replace('www.', '', $homeserver);
        }

        return $homeserver;
    }
}
