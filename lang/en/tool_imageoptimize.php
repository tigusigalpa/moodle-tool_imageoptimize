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
 * Strings for component 'tool_imageoptimize', language 'en'
 *
 * @package   tool_imageoptimize
 * @copyright 2020 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Image optimization';

$string['files_types'] = 'Files types';
$string['files_types_desc'] = 'Select the file types to which optimization will be applied';
$string['create_desc'] = 'Apply optimization on a first file upload';
$string['update_desc'] = 'Apply optimization on a file update';
$string['more_than'] = 'Optimize files more than, Kb';
$string['os_warning'] = 'At the moment, the plugin does not support working with the operating system of your server: <strong>{$a}</strong>';
$string['exec_warning'] = 'For the functioning of this plugin, you need to make some settings of your server
                        <ol>
                            <li>Enable php core function <strong>
                            <a href="https://www.php.net/manual/en/function.exec.php" 
                            target="_blank">exec()</a></strong>
                                <ol>
                                    <li>find the path to the configuration file <strong>php.ini</strong></li>
                                    <li>edit the list of disabled functions in this directive <strong>
                                    <a href="https://www.php.net/manual/en/ini.core.php#ini.disable-functions" 
                                    target="_blank">disable_functions</a></strong> in this file, delete from the list 
                                    <strong>exec</strong> and save changes</li>
                                    <li>restart php</li>
                                </ol>
                            </li>
                            <li>Install the packages necessary for compression for each image type on the server</li>
                        </ol>';
$string['info_title'] = 'To extend the format compression function <strong>{$a}</strong>, you can install additional packages for compression';
$string['warning_title'] = 'For the option to appear, install one of the packages on your server or all together';
$string['jpegoptim'] = '<strong><a href="http://freshmeat.sourceforge.net/projects/jpegoptim" target="_blank">JpegOptim</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install jpegoptim</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install jpegoptim</code></li>
                        </ul>';
$string['optipng'] = '<strong><a href="http://optipng.sourceforge.net/" target="_blank">OptiPNG</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install optipng</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install optipng</code></li>
                        </ul>';
$string['gifsicle'] = '<strong><a href="http://www.lcdf.org/gifsicle/" target="_blank">Gifsicle</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install gifsicle</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install gifsicle</code></li>
                        </ul>';
$string['webp'] = '<strong><a href="https://developers.google.com/speed/webp" target="_blank">WebP</a></strong>
                        <ul>
                            <li><strong>Debian/Ubuntu</strong>: <code>sudo apt-get install webp</code></li>
                            <li><strong>Fedora/RHEL/CentOS</strong>: <code>sudo dnf install libwebp-tools</code></li>
                        </ul>';

$string['privacy:metadata'] = 'The image optimize tool plugin does not store any personal data.';

// Strings for optimization task.
$string['backgroundoptimizingheading'] = 'Settings for background optimizing';
$string['cantfindfile'] = 'The file could not be found in filestorage.';
$string['cantcopyfile'] = 'The file could not be stored.';
$string['enablebackgroundoptimizing'] = 'Enable background optimizing';
$string['enablebackgroundoptimizing_desc'] = 'When backgroundoptimizing is enabled there will be no optimiziation during upload.';
$string['filecheckfailed'] = 'The filecheck failed.';
$string['taskoptimize'] = 'Backgrond image optimization task';
$string['taskoptimize_fill_table'] = 'Populating the tool_imageoptimize_files table';
$string['maxchunksize'] = 'Maximum number of files processed with one task call.';
$string['maxchunksizeimport'] = 'Maximum number of files written to the processing table with one task call.';
$string['filehasnofilesize'] = 'The file has no filesize or filesize 0. Abort!';
$string['filesortorder'] = 'Select filesortorder';
$string['filesortorder_id_asc'] = 'Start with oldest files';
$string['filesortorder_id_desc'] = 'Start with newest files';
$string['pathnamehashchanged'] = 'The Pathnamehash has changed. Abort!';