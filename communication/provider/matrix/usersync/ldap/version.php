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
 * TODO Describe Sync prodiver.
 *
 * @package    matrixusersync_ldap
 * @copyright  2025 André Menrath <andre.menrath@posteo.de>, University of Graz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined(constant_name: 'MOODLE_INTERNAL') || die();

$plugin->component = 'matrixusersync_ldap';
$plugin->version = 2025012700;
$plugin->requires = 2024041600;
$plugin->maturity = MATURITY_ALPHA;
