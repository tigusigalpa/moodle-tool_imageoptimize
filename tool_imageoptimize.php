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
 * Base class for the plugin
 *
 * @package   tool_imageoptimize
 * @copyright 2020 Igor Sazonov <sovletig@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('vendor/autoload.php');
use Spatie\ImageOptimizer\OptimizerChainFactory;

class tool_image_optimize {
    /**
     * Check OS
     *
     * @var bool
     */
    protected $oscheck = false;

    /**
     * PHP exec() function enabled
     *
     * @var bool
     */
    protected $exec = false;

    /**
     * $DB object
     *
     * @var object|\stdClass|null
     */
    protected $db = null;

    /**
     * $CFG object
     *
     * @var object|\stdClass|null
     */
    protected $cfg = null;

    /**
     * File record object
     *
     * @var \stdClass|null
     */
    protected $filerecord = null;

    /**
     * Source file record object (update hook)
     *
     * @var \stdClass|null
     */
    protected $sourcefilerecord = null;

    /**
     * JpegOptim package enabled
     *
     * @var bool
     */
    protected $jpegoptim = false;

    /**
     * OptiPNG package enabled
     *
     * @var bool
     */
    protected $optipng = false;

    /**
     * Gifsicle package enabled
     *
     * @var bool
     */
    protected $gifsicle = false;

    /**
     * WebP package enabled
     *
     * @var bool
     */
    protected $webp = false;

    /**
     * Current component name
     *
     * @var string
     */
    protected const COMPONENT = 'tool_imageoptimize';

    public const PACKAGES = [
        'jpegoptim', 'optipng', 'gifsicle'
    ];

    public const PACKAGES_WEBP = [
        'cwebp', 'dwebp', 'vwebp', 'webpmux'
    ];

    public const DEFAULTS = [
        'jpg' => 0,  // +++ MBS-HACK (Peter Mayer).
        'png' => 0,  // +++ MBS-HACK (Peter Mayer).
        'gif' => 0,
    ];

    public const PACKAGES_TYPES = [
        'jpg' => ['jpegoptim', 'webp'],
        'png' => ['optipng'],
        'gif' => ['gifsicle']
    ];

    public const FILES_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif'
    ];

    public function __construct(stdClass $filerecord = null, stdClass $sourcefilerecord = null) {
        global $CFG, $DB;
        $this->cfg = $CFG;
        $this->db = $DB;
        if ($filerecord) {
            $this->filerecord = $filerecord;
        }
        if ($sourcefilerecord) {
            $this->sourcefilerecord = $sourcefilerecord;
        }
        $this->oscheck = $this->os_check();
        // $this->exec = $this->exec_enabled();
        // $this->jpegoptim = $this->check_package('jpegoptim');
        // $this->optipng = $this->check_package('optipng');
        // $this->gifsicle = $this->check_package('gifsicle');
        // $this->webp = $this->check_package('webp');
    }

    /**
     * Get $exec value
     *
     * @return bool
     */
    public function get_exec() : bool {
        return $this->exec;
    }

    /**
     * Get $oscheck value
     *
     * @return bool
     */
    public function get_os_check() : bool {
        return $this->oscheck;
    }

    /**
     * Get the plugin config
     *
     * @param string $name
     *
     * @return mixed
     * @throws \dml_exception
     */
    public static function get_config(string $name = '') {
        return get_config(self::COMPONENT, $name ? $name : null);
    }

    /**
     * Converting kilobytes to bytes
     *
     * @param int $kb Kilobytes
     *
     * @return int
     */
    public static function kb_to_b(int $kb) : int {
        return $kb * 1024;
    }

    /**
     * Check php exec function enabled
     *
     * @return bool
     */
    protected function exec_enabled() : bool {
        if ($this->get_os_check()) {
            return !in_array('exec', explode(',', ini_get('disable_functions')));
        }
        return false;
    }

    /**
     * Get check OS can work with the plugin
     *
     * @return string
     */
    public function os_check() : string {
        switch ($this->cfg->ostype) {
            case 'UNIX':
                return $this->cfg->ostype;
        }
        return '';
    }

    /**
     * Check package exists on OS type
     *
     * @param string $name Package name
     *
     * @return bool
     */
    protected function check_package_command(string $name) : bool {
        switch ($this->get_os_check()) {
            case 'UNIX':
                if (!exec('which ' . $name)) {
                    // +++ MBS-HACK (Peter Mayer) Do not install  the needed packages automatically. (MBS-5165)
                    // This is important for moodle instances with a cluster of webservers. Because the script only runs once.
                    return false;
                    // --- MBS-HACK (Peter Mayer)

                    if (!exec('rpm -qa | grep ' . $name) || !exec('rpm -qa | grep -i ' . $name)
                        || !exec('yum list installed|grep \'' . $name . '\'')) {
                        if (!exec('dpkg -s ' . $name)) {
                            return false;
                        }
                    }
                }
                return true;
        }
        return false;
    }

    /**
     * Check needle server package installed
     *
     * @param string $name Package name
     *
     * @return bool
     */
    public function check_package(string $name) : bool {
        if ($this->get_exec()) {
            if ($name != 'webp') {
                if (in_array($name, self::PACKAGES)) {
                    if ($this->check_package_command($name)) {
                        return true;
                    }
                }
            } else {
                foreach (self::PACKAGES_WEBP as $webPLib) {
                    if (!$this->check_package_command($webPLib)) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Get an extension by the file record mime type
     *
     * @return string
     */
    public function get_extension() : string {
        if (isset(self::FILES_TYPES[$this->filerecord->mimetype])
            && !empty(self::FILES_TYPES[$this->filerecord->mimetype])) {
            return self::FILES_TYPES[$this->filerecord->mimetype];
        }
        return '';
    }

    /**
     * Can handle the given file extension
     *
     * @param string $ext File extension
     *
     * @return bool
     */
    public function can_handle_file_extension(string $ext = '') : bool {
        if ($ext = $ext ? $ext : $this->get_extension()) {
            switch ($ext) {
                case 'jpg':
                    return $this->jpegoptim || $this->webp;
                case 'png':
                    return $this->optipng;
                case 'gif':
                    return $this->gifsicle;
            }
        }
        return false;
    }

    /**
     * Can handle the given file
     *
     * @param string $action
     *
     * @return bool
     * @throws \dml_exception
     */
    protected function can_handle(string $action) : bool {
        if (self::get_config($action)) {
            $moreThan = self::get_config('more_than');
            if ($this->filerecord->filesize > 0
                && (!$moreThan || ($moreThan > 0 && (self::kb_to_b($moreThan) < $this->filerecord->filesize)))
                && $this->filerecord->filearea !== 'draft') {
                if (isset($this->filerecord->mimetype) && !empty($this->filerecord->mimetype)) {
                    if (isset(self::FILES_TYPES[$this->filerecord->mimetype])
                        && !empty(self::FILES_TYPES[$this->filerecord->mimetype])) {
                        if ($this->can_handle_file_extension()) {
                            if (self::get_config(self::FILES_TYPES[$this->filerecord->mimetype] . '_enabled')) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Handle optimize process
     *
     * @param string $action
     *
     * @return bool
     * @throws \dml_exception
     */
    public function handle(string $action) : bool {
        if ($this->can_handle($action)) {
            if ($fileStorage = get_file_storage()) {
                if ($fileSystem = $fileStorage->get_file_system()) {
                    if ($instance = $fileStorage->get_file_by_id($this->filerecord->id)) {
                        $fromFileContent = $fileSystem->get_content($instance);
                        $fromFileSourcePath = $fileSystem->get_local_path_from_storedfile($instance);
                        $fromFilePath = $this->temp_file_path();
                        $toFilePath = $this->temp_file_path();
                        file_put_contents($fromFilePath, $fromFileContent);
                        if (file_exists($fromFilePath)) {
                            $optimizerChain = OptimizerChainFactory::create();
                            $optimizerChain->optimize($fromFilePath, $toFilePath);
                            $toFileContent = file_get_contents($toFilePath);
                            $this->filerecord->pathnamehash = \file_storage::get_pathname_hash(
                                $this->filerecord->contextid,
                                $this->filerecord->component,
                                $this->filerecord->filearea,
                                $this->filerecord->itemid,
                                $this->filerecord->filepath,
                                $this->filerecord->filename
                            );
                            list($this->filerecord->contenthash, $this->filerecord->filesize, $newFile) = $fileSystem
                                ->add_file_from_string($toFileContent);
                            if ($newFile) {
                                $this->db->update_record('files', $this->filerecord);
                                @unlink($fromFileSourcePath);
                            }
                            @unlink($fromFilePath);
                            @unlink($toFilePath);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Temporary file path from input file name
     *
     * @return string
     */
    public function temp_file_path() : string {
        return sys_get_temp_dir() . '/' . random_string() . '.' . $this->get_extension();
    }
}
