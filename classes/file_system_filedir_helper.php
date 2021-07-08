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
 * Helper class for file system filedir.
 *
 * @package   tool_imageoptimize
 * @copyright 2021 ISB Bayern
 * @author    Peter Mayer, peter.mayer@isb.bayern.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_imageoptimize;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for file system filedir.
 *
 * @package   tool_imageoptimize
 * @copyright 2021 ISB Bayern
 * @author    Peter Mayer, peter.mayer@isb.bayern.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_system_filedir_helper extends \file_system_filedir {

    /**
     * Get instance of file_system_filedir_helper.
     * @return file_system_filedir_helper
     */
    public static function get_instance() :object {
        static $helper;

        // Force new instance while executing unit test as config may have
        // changed in various testcases.
        $forcenewinstance = (defined('PHPUNIT_TEST') && PHPUNIT_TEST);

        if (isset($helper) && !$forcenewinstance) {
            return $helper;
        }
        $helper = new file_system_filedir_helper();
        return $helper;
    }

    /**
     * Calls a protected method from file_system_filedir.
     * @param string $contenthash
     * @return string
     */
    public function get_fulldir_from_hash_imgopt($contenthash): string {
        return $this->get_fulldir_from_hash($contenthash);
    }

    /**
     * Calls a protected method from file_system.
     * @param string $contenthash
     * @param string $pathname
     * @return array The content hash (it might change) and file size
     */
    public function validate_hash_and_file_size_imgopt($contenthash, $pathname) :array {
        return $this->validate_hash_and_file_size($contenthash, $pathname);
    }

}
