<?php

use communication_matrix\external\set_usersync;
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
 * Management for the active user sync plugin.
 *
 * @package   communication_matrix
 * @copyright 2025 André Menrath <andre.menrath@uni-graz.at>, University of Graz
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once("{$CFG->libdir}/adminlib.php");

$action = optional_param('action', '', PARAM_ALPHA);
$plugin = optional_param('plugin', '', PARAM_PLUGIN);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/communication/provider/matrix/usersyncplugin.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
require_sesskey();

$manager = \core_plugin_manager::resolve_plugininfo_class('matrixusersync');
$pluginname = get_string('pluginname', "matrixusersync_{$plugin}");

$pluginname = \communication_matrix\external\set_usersync::set_matrixusersync_option($plugin);

\core\notification::add(
    get_string('usersyncset', 'communication_matrix', $pluginname),
    \core\notification::SUCCESS
);

redirect(new moodle_url('/admin/settings.php', [
    'section' => 'matrixsettings',
]));
