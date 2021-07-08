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
 * local_mbs upgrade related helper functions
 *
 * @package    tool_imageoptimize
 * @copyright  2021 ISB Bayern, Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Clean up table config_log data
 * @return void
 */
function tool_imageoptimize_create_table() : void {
    global $DB;

    $dbman = $DB->get_manager();
    $table = new xmldb_table('tool_imageoptimize_files');

    // Adding fields to table tool_imageoptimize_files.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('fileid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
    $table->add_field('contenthashold', XMLDB_TYPE_CHAR, '40', null, null, null, null);
    $table->add_field('contenthashnew', XMLDB_TYPE_CHAR, '40', null, null, null, null);
    $table->add_field('filesize', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
    $table->add_field('filesizeold', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
    $table->add_field('timeprocessed', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('filenotfound', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

    // Adding keys to table tool_imageoptimize_files.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Adding indexes to table tool_imageoptimize_files.
    $table->add_index('fileid', XMLDB_INDEX_UNIQUE, ['fileid']);
    $table->add_index('timeprocessed', XMLDB_INDEX_NOTUNIQUE, ['timeprocessed']);

    // Conditionally launch create table for tool_imageoptimize_files.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    // Adding Index to files table to speed up files-to-process search.
    $table = new xmldb_table('files');
    $index = new xmldb_index('mimetype', XMLDB_INDEX_NOTUNIQUE, ['mimetype']);

    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

}
