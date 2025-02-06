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
 * Example settings page for Manual Matrix User ID sync.
 *
 * @package    matrixusersync_manual
 * @copyright  2025 André Menrath <andre.menrath@uni-graz.at>, University of Graz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('matrixusersync_manual_settings', new lang_string('pluginname', 'matrixusersync_manual'));

    if ($ADMIN->fulltree) {
        $name = 'matrixusersync_manual/matrixmainsettings';
        $title = get_string('settingsheading', 'matrixusersync_manual', null, true);
        $settings->add(new admin_setting_heading($name, $title, null));
    }
}
