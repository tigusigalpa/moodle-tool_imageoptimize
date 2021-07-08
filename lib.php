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
 * Local lib code
 *
 * @package   tool_imageoptimize
 * @copyright 2020 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use tool_imageoptimize\tool_image_optimize_helper;

require_once('tool_imageoptimize.php');

/**
 * Handle 'after_file_created' hook
 *
 * @param stdClass $filerecord File record object
 *
 * @return bool
 * @throws \dml_exception
 */
function tool_imageoptimize_after_file_created(stdClass $filerecord) {
    $imageoptimizehelper = tool_image_optimize_helper::get_instance();
    $imageoptimizehelper->get_enabled_mimetypes();

    if(!in_array($filerecord->mimetype,$imageoptimizehelper->enabledmimetypes)){
        return false;
    }

    if (empty(get_config('tool_imageoptimize', 'enablebackgroundoptimizing'))) {
        $obj = new tool_image_optimize($filerecord);
        return $obj->handle('create');
    }

    $imageoptimizehelper->insert_fileinfo_depending_on_contenthash($filerecord);
}

/**
 * Handle 'after_file_updated' hook
 *
 * @param stdClass $filerecord
 * @param stdClass $sourcefilerecord
 *
 * @return bool
 * @throws \dml_exception
 */
function tool_imageoptimize_after_file_updated(stdClass $filerecord, stdClass $sourcefilerecord) {

    $imageoptimizehelper = tool_image_optimize_helper::get_instance();
    $imageoptimizehelper->get_enabled_mimetypes();

    if(!in_array($filerecord->mimetype,$imageoptimizehelper->enabledmimetypes)){
        return false;
    }

    if (empty(get_config('tool_imageoptimize', 'enablebackgroundoptimizing'))) {
        $obj = new tool_image_optimize($filerecord, $sourcefilerecord);
        return $obj->handle('update');
    }

}

/**
 * Handle 'after_file_deleted' hook
 * 
 * @param stdClass $fielobject
 */
function tool_imageoptimize_after_file_deleted(stdClass $fileobject) {
    global $DB;

    $imageoptimizehelper = tool_image_optimize_helper::get_instance();
    $imageoptimizehelper->get_enabled_mimetypes();

    if (!in_array($fileobject->mimetype, $imageoptimizehelper->enabledmimetypes)) {
        return false;
    }
    
    $DB->delete_records('tool_imageoptimize_files', ['contenthashold' => $fileobject->contenthash]);
}