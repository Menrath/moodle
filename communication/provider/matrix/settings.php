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

/**
 * Matrix communication plugin settings.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use communication_matrix\table\usersync_management_table;

$ADMIN->add('communicationsettings', new admin_category('matrix', get_string('matrixsettings', 'communication_matrix')));

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'matrixsettings',
        get_string('matrixgeneralsettings', 'communication_matrix'),
        'moodle/site:config'
    );

    // Create settings heading for the main configuration of the managed Matrix server and the service user for Moodle.
    $name = 'communication_matrix/matrixmainsettings';
    $title = get_string('matrixmainsettings', 'communication_matrix', null, true);
    $settings->add(new admin_setting_heading($name, $title, null));

    // Home server URL.
    $name = new lang_string('matrixhomeserverurl', 'communication_matrix');
    $desc = new lang_string('matrixhomeserverurl_desc', 'communication_matrix');
    $settings->add(new admin_setting_configtext('communication_matrix/matrixhomeserverurl', $name, $desc, ''));

    // Access token.
    $name = new lang_string('matrixaccesstoken', 'communication_matrix');
    $desc = new lang_string('matrixaccesstoken_desc', 'communication_matrix');
    $settings->add(new admin_setting_configpasswordunmask('communication_matrix/matrixaccesstoken', $name, $desc, ''));

    // Element web URL.
    $name = new lang_string('matrixelementurl', 'communication_matrix');
    $desc = new lang_string('matrixelementurl_desc', 'communication_matrix');
    $settings->add(new admin_setting_configtext('communication_matrix/matrixelementurl', $name, $desc, ''));

    // Create settings heading for the main configuration of the managed Matrix server and the service user for Moodle.
    $name = 'communication_matrix/matrixusermappingettings';
    $title = get_string('matrixusermappingettings', 'communication_matrix', null, true);
    $settings->add(new admin_setting_heading($name, $title, null));

    // Sub-Plugin used for User Synchronization.
    $settings->add(new \core_admin\admin\admin_setting_plugin_manager(
        'matrixusersync',
        usersync_management_table::class,
        'matrix_usersync_settings',
        get_string('matrixusersyncsettings', 'communication_matrix'),
    ));
}

// Note: We add editortiny to the settings page here manually rather than deferring to the plugininfo class.
// This ensures that it shows in the category list too.
$ADMIN->add('matrix', $settings);


$ADMIN->add('matrix', new admin_category('matrixusersync', get_string('matrixusersyncsettings', 'communication_matrix')));


foreach (core_plugin_manager::instance()->get_plugins_of_type('matrixusersync') as $plugin) {
    /** @var \communication_matrix\plugininfo\matrixusersync $plugin */
    $plugin->load_settings($ADMIN, 'matrixusersync', $hassiteconfig);
}

// Required or the editor plugininfo will add this section twice.
unset($settings);
$settings = null;
