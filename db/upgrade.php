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
 * Handles upgrading instances of this plugin.
 *
 * @since      Moodle 3.8
 * @package    tool_imageoptimize
 * @copyright  2021 ISB Bayern
 * @author     Peter Mayer, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Handles upgrading instances of this plugin.

 * @param int $oldversion
 * @return bool
 */
function xmldb_tool_imageoptimize_upgrade($oldversion) {
    global $CFG;

    require_once($CFG->dirroot . "/admin/tool/imageoptimize/db/upgradelib.php");

    if ($oldversion < 2020060801) {

        // Define table tool_imageoptimize_files to be created.
        tool_imageoptimize_create_table();

        // Imageoptimize savepoint reached.
        upgrade_plugin_savepoint(true, 2020060801, 'tool', 'imageoptimize');
    }

    return true;
}
