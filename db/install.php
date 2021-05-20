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
 * Install trigger for component 'tool_imageoptimize'
 * @link      https://docs.moodle.org/dev/Installing_and_upgrading_plugin_database_tables#install.php
 * @package   tool_imageoptimize
 * @copyright 2020 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * Create tool_imageoptimize table on installation.
  */
function xmldb_tool_imageoptimize_install() {
    global $CFG;
    require_once($CFG->dirroot . "/admin/tool/imageoptimize/db/upgradelib.php");

    // Create imageoptimize table.
    tool_imageoptimize_create_table();

    return true;
}
