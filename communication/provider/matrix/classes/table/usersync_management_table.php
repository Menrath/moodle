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

namespace communication_matrix\table;

use core\url;
use core_admin\table\plugin_management_table;
use stdClass;

/**
 * Matrix User Sync admin settings.
 *
 * @package   communication_matrix
 * @copyright 2025 André Menrath <andre.menrath@uni-graz.at>, University of Graz
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usersync_management_table extends plugin_management_table {
    /**
     * Get the plugin type that is managed.
     *
     * @return string
     */
    protected function get_plugintype(): string {
        return 'matrixusersync';
    }

    /**
     * Get the guess for a settings URL of the sub-plugin.
     *
     * @return void
     */
    public function guess_base_url(): void {
        $this->define_baseurl(
            new url('/admin/settings.php', ['section' => 'matrixsettingsusersync'])
        );
    }

    /**
     * Get a list of the column titles
     *
     * @return string[]
     */
    protected function get_column_list(): array {
        $columns = [
            'name' => get_string('name', 'core'),
            'version' => get_string('version', 'core'),
        ];

        if ($this->supports_disabling()) {
            $columns['enabled'] = get_string('selected', 'form');
        }

        if ($this->supports_ordering()) {
            $columns['order'] = get_string('order', 'core');
        }

        $columns['settings'] = get_string('settings', 'core');

        return $columns;
    }

    /**
     * Get action url that is used, when JavaScript AJAX is not available.
     *
     * @param array $params
     * @return url
     */
    protected function get_action_url(array $params = []): url {
        return new url('/communication/provider/matrix/usersyncplugin.php', $params);
    }

    /**
     * Get the web service method used to toggle state.
     *
     * This is different from the core toggle service, because only one sub-plugin will be enabled and all others will be disabled.
     *
     * @return null|string
     */
    protected function get_toggle_service(): ?string {
        return 'communication_matrix_set_usersync';
    }

    /**
     * Show the enable/disable column content.
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_enabled(stdClass $row): string {
        global $OUTPUT;

        $enabled = $row->plugininfo->is_enabled();
        if ($enabled) {
            $labelstr = get_string('disableplugin', 'core_admin', $row->plugininfo->displayname);
        } else {
            $labelstr = get_string('enableplugin', 'core_admin', $row->plugininfo->displayname);
        }

        $params = [
            'id' => 'admin-toggle-' . $row->plugininfo->name,
            'checked' => $enabled,
            'dataattributes' => [
                'name' => 'id',
                'value' => $row->plugininfo->name,
                'toggle-method' => $this->get_toggle_service(),
                'action' => 'togglestate',
                'plugin' => $row->plugin,
                'state' => $enabled ? 1 : 0,
            ],
            'title' => $labelstr,
            'label' => $labelstr,
            'labelclasses' => 'sr-only',
        ];

        return $OUTPUT->render_from_template('core_admin/setting_configradio', $params);
    }

}
