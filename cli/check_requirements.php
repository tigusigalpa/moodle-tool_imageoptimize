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
 * With this script it can be checked if all needed modules are installed on that server used to process background optimization.
 *
 * @package    tool_imageoptimize
 * @copyright  2021 ISB Bayern
 * @author     Peter Mayer, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . "/clilib.php");
require_once($CFG->dirroot . '/admin/tool/imageoptimize/tool_imageoptimize.php');
\core_php_time_limit::raise();

use \tool_imageoptimize\tool_image_optimize_helper;

$imageoptimizehelper = tool_image_optimize_helper::get_instance();

$alloverstatus = true;


$imageoptimize = new tool_image_optimize();

if (!$imageoptimize->os_check()) {
    mtrace("Your OS is not compatible. Please consult the documentation.");
    mtrace("Abort!");
    set_config('oscompatible', false, 'tool_imageoptimize');
    die;
} else {
    set_config('oscompatible', true, 'tool_imageoptimize');
}

if (!$imageoptimize->exec_enabled()) {
    mtrace("Enable php core function exec() first.");
    mtrace("Abort!");
    set_config('execenabled', false, 'tool_imageoptimize');
    die;
} else {
    set_config('execenabled', true, 'tool_imageoptimize');
}

foreach (array_keys($imageoptimize::PACKAGES) as $package) {
    if (!$imageoptimizehelper->check_package_command_for_testing($package)) {
        mtrace($package . " is missing! Please execute:\napt-get install " . $package);
        set_config($package . '_installed', false, 'tool_imageoptimize');
        $alloverstatus = false;
    } else {
        set_config($package . '_installed', true, 'tool_imageoptimize');
    }
}

if ($alloverstatus) {
    mtrace("All modules are installed.");
}
