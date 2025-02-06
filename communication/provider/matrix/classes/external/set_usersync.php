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

namespace communication_matrix\external;

use core_admin\external\set_plugin_state;
use communication_matrix\plugininfo\matrixusersync;

/**
 * Implementation of web service communication_matrix_set_usersync
 *
 * @package    communication_matrix
 * @copyright  2025 André Menrath <andre.menrath@uni-graz.at>, University of Graz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_usersync extends set_plugin_state {
    /**
     * Set the used matrixusersync plugin for synchronizing the Matrix User ID in the Matrix Communication Provider.
     *
     * @param string $plugin The name of the plugin
     * @param int $state The target state
     * @return null
     */
    public static function execute(
        string $plugin,
        int $state,
    ): array {
        [
            'plugin' => $plugin,
            'state' => $state,
        ] = self::validate_parameters(self::execute_parameters(), [
            'plugin' => $plugin,
            'state' => $state,
        ]);

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        $plugin = self::set_matrixusersync_option($plugin);

        return [];
    }

    /**
     * Set used Plugin for the Matrix User ID synchronization.
     *
     * @package    communication_matrix
     *
     * @param string $pluginname The matrixusersync plugin to use.
     * @return string
     */
    public static function set_matrixusersync_option($pluginname = 'matrixusersync_username'): string {
        $plugins = \core_plugin_manager::instance()->get_subplugins_of_plugin( 'communication_matrix' );

        if (!array_key_exists( $pluginname, $plugins)) {
            $pluginname = 'matrixusersync_username';
        }

        foreach ($plugins as $pluginfullname => $plugindata) {
            if ( $pluginfullname === $pluginname) {
                matrixusersync::enable_plugin($plugindata->name, 1);
            } else {
                matrixusersync::enable_plugin($plugindata->name, 0);
            }
        }

        return $pluginname;
    }
}
