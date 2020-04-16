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

namespace Tigusigalpa\Moodle\Admin\Tool\ImageOptimize;

use Spatie\ImageOptimizer\OptimizerChainFactory;

defined('MOODLE_INTERNAL') || die;

class ImageOptimize
{
    /**
     * Check OS
     *
     * @var bool
     */
    protected $osCheck = false;

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
    protected $fileRecord = null;

    /**
     * Source file record object (update hook)
     *
     * @var \stdClass|null
     */
    protected $sourceFileRecord = null;

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
     * pngquant package enabled
     *
     * @var bool
     */
    protected $pngquant = false;

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
     * Component temporary directory
     *
     * @var string
     */
    protected $tempDir = '';

    /**
     * Current component name
     *
     * @var string
     */
    protected const COMPONENT = 'tool_imageoptimize';

    public const PACKAGES = [
        'jpegoptim', 'optipng', 'pngquant', 'gifsicle',
    ];

    public const PACKAGES_WEBP = [
        'cwebp', 'dwebp', 'vwebp', 'webpmux',
    ];

    public const DEFAULTS = [
        'jpg' => 1,
        'png' => 1,
        'gif' => 0,
    ];

    public const PACKAGES_TYPES = [
        'jpg' => ['jpegoptim', 'webp'],
        'png' => ['optipng', 'pngquant', 'webp'],
        'gif' => ['gifsicle'],
    ];

    public const FILES_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
    ];

    public function __construct(\stdClass $fileRecord = null, \stdClass $sourceFileRecord = null)
    {
        global $CFG, $DB;
        $this->cfg = $CFG;
        $this->db = $DB;
        $this->tempDir = $this->cfg->tempdir . '/' . self::COMPONENT;
        if (!file_exists($this->tempDir) || !is_dir($this->tempDir)) {
            @mkdir($this->tempDir, 0755);
        }
        if ($fileRecord) {
            $this->fileRecord = $fileRecord;
        }
        if ($sourceFileRecord) {
            $this->sourceFileRecord = $sourceFileRecord;
        }
        $this->osCheck = $this->osCheck();
        $this->exec = $this->execEnabled();
        $this->jpegoptim = $this->checkPackage('jpegoptim');
        $this->optipng = $this->checkPackage('optipng');
        $this->pngquant = $this->checkPackage('pngquant');
        $this->gifsicle = $this->checkPackage('gifsicle');
        $this->webp = $this->checkPackage('webp');
    }

    /**
     * Get $exec value
     *
     * @return bool
     */
    public function getExec() : bool
    {
        return $this->exec;
    }

    /**
     * Get $osCheck value
     *
     * @return bool
     */
    public function getOSCheck() : bool
    {
        return $this->osCheck;
    }

    /**
     * Get the plugin config
     *
     * @param string $name
     *
     * @return mixed
     * @throws \dml_exception
     */
    public static function getConfig(string $name = '')
    {
        return get_config(self::COMPONENT, $name ? $name : null);
    }

    /**
     * Converting kilobytes to bytes
     *
     * @param int $kb Kilobytes
     *
     * @return int
     */
    public static function kbToB(int $kb) : int
    {
        return $kb * 1024;
    }

    /**
     * Check php exec function enabled
     *
     * @return bool
     */
    protected function execEnabled() : bool
    {
        if ($this->getOSCheck()) {
            return !in_array('exec', explode(',', ini_get('disable_functions')));
        }
        return false;
    }

    /**
     * Get check OS can work with the plugin
     *
     * @return string
     */
    public function osCheck() : string
    {
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
    protected function checkPackageCommand(string $name) : bool
    {
        switch ($this->getOSCheck()) {
            case 'UNIX':
                if (!exec('which ' . $name)) {
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
    public function checkPackage(string $name) : bool
    {
        if ($this->getExec()) {
            if ($name != 'webp') {
                if (in_array($name, self::PACKAGES)) {
                    if ($this->checkPackageCommand($name)) {
                        return true;
                    }
                }
            } else {
                foreach (self::PACKAGES_WEBP as $webPLib) {
                    if (!$this->checkPackageCommand($webPLib)) {
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
    public function getExtension() : string
    {
        if (isset(self::FILES_TYPES[$this->fileRecord->mimetype])
            && !empty(self::FILES_TYPES[$this->fileRecord->mimetype])) {
            return self::FILES_TYPES[$this->fileRecord->mimetype];
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
    public function canHandleFileExtension(string $ext = '') : bool
    {
        if ($ext = $ext ? $ext : $this->getExtension()) {
            switch ($ext) {
                case 'jpg':
                    return ($this->jpegoptim || $this->webp) ? true : false;
                case 'png':
                    return ($this->optipng || $this->pngquant || $this->webp) ? true : false;
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
    protected function canHandle(string $action) : bool
    {
        if (self::getConfig($action)) {
            $moreThan = self::getConfig('more_than');
            if ($this->fileRecord->filesize > 0
                && (!$moreThan || ($moreThan > 0 && (self::kbToB($moreThan) < $this->fileRecord->filesize)))
                && $this->fileRecord->filearea !== 'draft') {
                if (isset($this->fileRecord->mimetype) && !empty($this->fileRecord->mimetype)) {
                    if (isset(self::FILES_TYPES[$this->fileRecord->mimetype])
                        && !empty(self::FILES_TYPES[$this->fileRecord->mimetype])) {
                        if ($this->canHandleFileExtension()) {
                            if (self::getConfig(self::FILES_TYPES[$this->fileRecord->mimetype] . '_enabled')) {
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
    public function handle(string $action) : bool
    {
        if ($this->canHandle($action)) {
            if ($fileStorage = get_file_storage()) {
                if ($fileSystem = $fileStorage->get_file_system()) {
                    if ($instance = $fileStorage->get_file_by_id($this->fileRecord->id)) {
                        $fromFileContent = $fileSystem->get_content($instance);
                        $fromFileSourcePath = $fileSystem->get_local_path_from_storedfile($instance);
                        $fromFilePath = $this->tempFilePath();
                        $toFilePath = $this->tempFilePath();
                        file_put_contents($fromFilePath, $fromFileContent);
                        if (file_exists($fromFilePath)) {
                            $optimizerChain = OptimizerChainFactory::create();
                            $optimizerChain->optimize($fromFilePath, $toFilePath);
                            $toFileContent = file_get_contents($toFilePath);
                            $this->fileRecord->pathnamehash = \file_storage::get_pathname_hash(
                                $this->fileRecord->contextid,
                                $this->fileRecord->component,
                                $this->fileRecord->filearea,
                                $this->fileRecord->itemid,
                                $this->fileRecord->filepath,
                                $this->fileRecord->filename
                            );
                            list($this->fileRecord->contenthash, $this->fileRecord->filesize, $newFile) = $fileSystem
                                ->add_file_from_string($toFileContent);
                            if ($newFile) {
                                $this->db->update_record('files', $this->fileRecord);
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
    public function tempFilePath() : string
    {
        return $this->tempDir . '/' . random_string() . '.' . $this->getExtension();
    }
}
