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

require_once('image_optimize.php');

/**
 * Handle 'after_file_created' hook
 *
 * @param stdClass $filerecord File record object
 *
 * @return bool
 * @throws \dml_exception
 */
function tool_imageoptimize_after_file_created(stdClass $filerecord) {
    $obj = new tool_image_optimize($filerecord);
    return $obj->handle('create');
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
    $obj = new tool_image_optimize($filerecord, $sourcefilerecord);
    return $obj->handle('update');
}
