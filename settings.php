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
 * Images optimization script.
 *
 * @package   tool_imageoptimize
 * @copyright 2020 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once __DIR__ . '/autoload.php';

use Tigusigalpa\Moodle\Admin\Tool\ImageOptimize\ImageOptimize;
use Tigusigalpa\Moodle\Admin\Setting\Notification;

if ($hassiteconfig) {
    $imageOptimize = new ImageOptimize();
    $settings = new \admin_settingpage('tool_imageoptimize', get_string('pluginname', 'tool_imageoptimize'));
    $ADMIN->add('tools', $settings);

    if ($imageOptimize->getOSCheck()) {
        if (!$imageOptimize->getExec()) {
            $settings->add(new Notification(
                'tool_imageoptimize/exec_warning',
                'exec_warning',
                get_string('exec_warning', 'tool_imageoptimize'),
                'warning'
            ));
        } else {
            $settings->add(new \admin_setting_heading(
                'tool_imageoptimize/heading',
                get_string('files_types', 'tool_imageoptimize'),
                get_string('files_types_desc', 'tool_imageoptimize')
            ));
            foreach (array_keys(ImageOptimize::PACKAGES_TYPES) as $imageExtension) {
                if (!$imageOptimize->canHandleFileExtension($imageExtension)) {
                    $warning = get_string('warning_title', 'tool_imageoptimize') . '<ol>';
                    foreach (ImageOptimize::PACKAGES_TYPES[$imageExtension] as $package) {
                        $warning .= '<li>' . get_string($package, 'tool_imageoptimize') . '</li>';
                    }
                    $warning .= '</ol>';
                    $settings->add(new Notification(
                        'tool_imageoptimize/' . $imageExtension . '_head_warninig',
                        $imageExtension . '_head_warninig',
                        $warning,
                        'warning'
                    ));
                } else {
                    $info = '';
                    foreach (ImageOptimize::PACKAGES_TYPES[$imageExtension] as $package) {
                        if (!$imageOptimize->checkPackage($package)) {
                            $info .= '<li>' . get_string($package, 'tool_imageoptimize') . '</li>';
                        }
                    }
                    if ($info) {
                        $settings->add(new Notification(
                            'tool_imageoptimize/' . $imageExtension . '_warning',
                            $imageExtension . '_warning',
                            get_string(
                                'info_title',
                                'tool_imageoptimize',
                                \core_text::strtoupper($imageExtension)
                            ) . '<ol>' . $info . '</ol>'
                        ));
                    }
                    $settings->add(new \admin_setting_configcheckbox(
                        'tool_imageoptimize/' . $imageExtension . '_enabled',
                        \core_text::strtoupper($imageExtension),
                        '',
                        ImageOptimize::DEFAULTS[$imageExtension]
                    ));
                }
            }
            $settings->add(new \admin_setting_heading(
                'tool_imageoptimize/settings',
                get_string('settings'),
                ''
            ));
            foreach (['create', 'update'] as $action) {
                $settings->add(new \admin_setting_configcheckbox(
                    'tool_imageoptimize/' . $action,
                    get_string($action),
                    get_string($action . '_desc', 'tool_imageoptimize'),
                    1
                ));
            }
            $settings->add(new \admin_setting_configtext(
                'tool_imageoptimize/more_than',
                get_string('more_than', 'tool_imageoptimize'),
                '',
                0,
                PARAM_INT
            ));
        }
    } else {
        $settings->add(new Notification(
            'tool_imageoptimize/os_warning',
            'os_warning',
            get_string('os_warning', 'tool_imageoptimize', php_uname('s') . ' ' .
                php_uname('r') . ' ' . php_uname('v')),
            'warning'
        ));
    }
}
