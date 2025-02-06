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

namespace communication_matrix\plugininfo;

use core\plugininfo\base, core_plugin_manager;
use core\url;

/**
 * Base class for Matrix User ID synchronization plugins.
 *
 * @package    communication_matrix
 * @copyright  2025 André Menrath <andre.menrath@posteo.de>, University of Graz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrixusersync extends base {
    /**
     * These plugins can be disabaled.
     *
     * The current design only supports one active synchronization sub-plugin for the Matrix User Ids.
     * Therefore we need to be able to disable the others.
     *
     * @return bool
     */
    public static function plugintype_supports_disabling(): bool {
        return true;
    }

    /**
     * These subplugins can be uninstalled.
     *
     * @return bool
     */
    public function is_uninstall_allowed(): bool {
        if (in_array($this->name, \core_plugin_manager::standard_plugins_list('matrixusersync'))) {
            return false;
        }
        return true;
    }

    /**
     * Get the full classname of the matrix user manager.
     *
     * TODO: this is not at all safe.
     *
     * @return string
     */
    public function get_classname_of_matrix_user_manager(): string {
        return "\\{$this->type}_{$this->name}\\matrix_user_manager";
    }

    /**
     * Return URL used for management of plugins of this type.
     *
     * @return url
     */
    public static function get_manage_url(): url {
        return new url(
            '/admin/settings.php',
            [
                'section' => 'matrixsettingsusersync',
            ]);
    }

    /**
     * Include the settings.php file from subplugins if provided.
     *
     * This is a copy of very similar implementations from various other subplugin areas.
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(\part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig): void {
        // In case settings.php wants to refer to them.
        global $CFG, $USER, $DB, $OUTPUT, $PAGE;

        /** @var \admin_root $ADMIN */
        $ADMIN = $adminroot; // May be used in settings.php.
        $plugininfo = $this; // Also can be used inside settings.php.

        if (!$this->is_installed_and_upgraded()) {
            return;
        }

        if (!$hassiteconfig || !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $section = $this->get_settings_section_name();
        $settings = new \admin_settingpage(
            $section,
            $this->displayname,
            'moodle/site:config',
            $this->is_enabled() === false
        );

        // This may also set $settings to null.
        include($this->full_path('settings.php'));

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Get the settings section name.
     * This is used to get the setting links in the selection view for which sync is applied.
     *
     * @return null|string the settings section name.
     */
    public function get_settings_section_name(): ?string {
        if (!file_exists($this->full_path('settings.php'))) {
            return null;
        }

        return "matrixusersync_{$this->name}_settings";
    }

    /**
     * Returns the enabled Matrix User Sync plugin.
     *
     * @return array
     */
    public static function get_enabled_plugins(): array {
        $pluginmanager = \core_plugin_manager::instance();
        $plugins = $pluginmanager->get_installed_plugins('matrixusersync');

        if (!$plugins) {
            return [];
        }

        // Filter to return only enabled plugins.
        $enabled = [];
        foreach (array_keys($plugins) as $pluginname) {
            $disabled = get_config("matrixusersync_{$pluginname}", 'disabled');
            if (empty($disabled)) {
                $enabled[$pluginname] = $pluginname;
            }
        }
        return $enabled;
    }

    /**
     * Enable plugin.
     *
     * @param string $plugin
     * @param int $enabled
     * @return bool
     */
    public static function enable_plugin(string $plugin, int $enabled): bool {
        $pluginname = "matrixusersync_{$plugin}";

        $oldvalue = !empty(get_config($pluginname, 'disabled'));
        $disabled = empty($enabled);
        $haschanged = false;

        // Only set value if there is no config setting or if the value is different from the previous one.
        if (!$oldvalue && $disabled) {
            set_config('disabled', $disabled, $pluginname);
            $haschanged = true;
        } else if ($oldvalue && !$disabled) {
            unset_config('disabled', $pluginname);
            $haschanged = true;
        }

        if ($haschanged) {
            add_to_config_log('disabled', $oldvalue, $disabled, $pluginname);
            \core_plugin_manager::reset_caches();
        }

        return $haschanged;
    }
}
