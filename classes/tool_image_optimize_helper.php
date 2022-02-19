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
 * Helper class for image optimizer.
 *
 * @package   tool_imageoptimize
 * @copyright 2021 ISB Bayern
 * @author    Peter Mayer, peter.mayer@isb.bayern.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_imageoptimize;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/admin/tool/imageoptimize/vendor/autoload.php');
require_once($CFG->dirroot . '/admin/tool/imageoptimize/tool_imageoptimize.php');
use coding_exception;
use \Spatie\ImageOptimizer\OptimizerChainFactory;
use stdClass;

/**
 * Helper class for image optimizer.
 *
 * @package   tool_imageoptimize
 * @copyright 2021 ISB Bayern
 * @author    Peter Mayer, peter.mayer@isb.bayern.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_image_optimize_helper extends \tool_image_optimize {

    /** @var object of config. */
    public $config;

    /** @var array of enabled mimetypes. */
    public $enabledmimetypes = [];

    /** @var bool of dryrun */
    public $dryrun = false;

    /** @var bool called by task */
    private $calledbytask = false;

    /**
     * Load config, make the constructor private to force singleton.
     */
    private function __construct() {
        $this->config = get_config('tool_imageoptimize');
    }

    /**
     * Get instance of tool_image_optimize_helper.
     * @return tool_image_optimize_helper
     */
    public static function get_instance() :object {
        static $helper;

        // Force new instance while executing unit test as config may have
        // changed in various testcases.
        $forcenewinstance = (defined('PHPUNIT_TEST') && PHPUNIT_TEST);

        if (isset($helper) && !$forcenewinstance) {
            return $helper;
        }
        $helper = new tool_image_optimize_helper();
        return $helper;
    }

    /**
     * Initialize filestorage if necessary.
     * @return file_storage
     */
    protected function init_filestorage() :object {
        if (isset($this->filestorage)) {
            return $this->filestorage;
        }
        $this->filestorage = get_file_storage();
        return $this->filestorage;
    }

    /**
     * Get all enabled mimetypes.
     * @return array
     */
    public function get_enabled_mimetypes() :array {

        if (empty($this->enabledmimetypes)) {
            $this->enabledmimetypes = [];
            foreach (array_keys(self::PACKAGES_TYPES) as $imageextension) {
                if (!empty($this->config->{$imageextension . '_enabled'})) {
                    $this->enabledmimetypes[] = array_search($imageextension, self::FILES_TYPES);
                }

            }
        }
        return $this->enabledmimetypes;
    }

    /**
     * Get Files to process.
     * @param int $maxchunksize
     * @return array
     */
    public function get_files_to_process($maxchunksize = 2000) :array {
        global $DB;

        $this->get_enabled_mimetypes();
        if (empty($this->enabledmimetypes)) {
            if (CLI_SCRIPT) {
                mtrace("No mimetypes are enabled for optimization.");
            }
            return [];
        }

        switch ($this->config->filessortorder) {
            case "idasc":
                $order = " ORDER BY fileid ASC";
                break;

            case "iddesc":
                $order = " ORDER BY fileid DESC";
                break;

            default:
                $order = '';
                break;
        }

        // These complex series of queries is necessary, to get only one file reference for a specific contenthash.
        // Otherwise there could be two files in one chunk with the same contenthash.
        // Because, all references would be processed by the first occurence.
        // The second call can not find the file with the old contenthash any more and mark it as 'not found'.
        // It could not be done by a simple group by because of PostgreSQL support.
        $sql = "SELECT DISTINCT fileid, contenthashold
                FROM {tool_imageoptimize_files}
                WHERE (timeprocessed is null OR timeprocessed = 0) " . $order;

        // Use e.g. 2 * $maxchunksize because it is possible to get multiple times the same contenthash.
        $rows = $DB->get_records_sql($sql, null, 0, 2 * $maxchunksize);

        foreach ($rows as $row) {
            $fileids[$row->contenthashold] = $row->fileid;
        }

        if (empty($fileids)) {
            return [];
        }

        $fileids = array_values($fileids);

        list($sqlin, $inparms) = $DB->get_in_or_equal($fileids);
        $sql = "SELECT * from {files} WHERE id " . $sqlin;
        return $DB->get_records_sql($sql, $inparms, 0, $maxchunksize);
    }

    /**
     * Process Files.
     * @param int $chunksize
     */
    public function process_files($chunksize = 2000) :void {
        global $DB;

        $filestoprocess = $this->get_files_to_process($chunksize);

        if (empty($filestoprocess)) {
            mtrace("No files found to be processed.");
        }

        foreach ($filestoprocess as $fileid => $file) {
            $fileobject = new \stdClass();
            $fileobject->fileid = $fileid;
            $fileobject->contenthashold = $file->contenthash;
            $fileobject->filesizeold = $file->filesize;

            try {
                $transaction = $DB->start_delegated_transaction();

                $this->process_file($file);

                if (!empty($this->dryrun)) {
                    mtrace("Delete File references because of Dryrun.");
                    // Delete all file references by contenthashold.
                    $DB->set_field(
                        'tool_imageoptimize_files',
                        'contenthashnew',
                        '',
                        ['contenthashold' => $fileobject->contenthashold]
                    );
                    $DB->set_field(
                        'tool_imageoptimize_files',
                        'timeprocessed',
                        '',
                        ['contenthashold' => $fileobject->contenthashold]
                    );
                    $DB->set_field(
                        'tool_imageoptimize_files',
                        'filesize',
                        '',
                        ['contenthashold' => $fileobject->contenthashold]
                    );
                }

                $transaction->allow_commit();
            } catch (\Exception $e) {
                $transaction->rollback($e);
            }
        }
    }

    /**
     * Handle optimize process.
     *
     * @param object $file
     *
     * @return bool false on failed process; true on success.
     * @throws \dml_exception
     */
    protected function process_file($file) : bool {
        global $DB;

        if (!$filestorage = $this->init_filestorage()) {
            // Exit if the filestorage could not be initiated.
            mtrace("Filestorage could not be initialized\n");
            return false;
        }

        if (!$filesystem = $filestorage->get_file_system()) {
            // Exit if the filesystem could not be initiated.
            mtrace("Filesystem could not be initialized\n");
            return false;
        }

        if (!$instance = $filestorage->get_file_by_id($file->id)) {
            // Exit if the file could not be found.
            mtrace("File id (" . $file->id . ") unknown\n");
            return false;
        }

        // Store old fileobject for later use.
        $fileold = new \stdClass();
        $fileold = clone $file;

        // Make sure, that the file is existing physically.
        $filesystemhelper = file_system_filedir_helper::get_instance();
        $filepath = $filesystemhelper->get_fulldir_from_hash_imgopt($file->contenthash);

        // If file not found and running dryrun.
        if (!file_exists($filepath . "/" . $file->contenthash) && !empty($this->dryrun)) {
            mtrace("File physically not found (" . $file->filename . ", " . $file->contenthash . ") = DRYRUN\n");
            return false;
        }

        // If file not found and NOT running dryrun.
        if (!file_exists($filepath . "/" . $file->contenthash) && empty($this->dryrun)) {
            mtrace("File physically not found (" . $file->filename . ", " . $file->contenthash . ")\n");
            $DB->set_field('tool_imageoptimize_files', 'filenotfound', 1, ['contenthashold' => $file->contenthash]);
            $DB->set_field('tool_imageoptimize_files', 'timeprocessed', time(), ['contenthashold' => $file->contenthash]);
            return false;
        }

        // Copy file to temp.
        $fromfilecontent = $filesystem->get_content($instance);
        $fromfilepath = $this->get_temp_file_path($file);

        // Create a working file.
        $tofilepath = $fromfilepath;

        if (!file_put_contents($fromfilepath, $fromfilecontent)) {
            // Exit if the file does not exist.
            return false;
        }

        // Optimize the image.
        // Camelcase is necessary, because OptimizerChainFactory is a third party class that is used here.
        $optimizerchain = OptimizerChainFactory::create();
        $optimizerchain->optimize($fromfilepath, $tofilepath);
        $tofilecontent = file_get_contents($tofilepath);

        // Recalculating the Pathnamehash. But should be the same as before.
        $file->pathnamehash = $filestorage->get_pathname_hash(
            $file->contextid,
            $file->component,
            $file->filearea,
            $file->itemid,
            $file->filepath,
            $file->filename
        );

        if ($file->pathnamehash != $fileold->pathnamehash) {
            // Throw error to revert db changes.
            throw new coding_exception('pathnamehashchanged');
        }

        // Adding the processed file to the file system.
        // Source: lib/filestorage/file_system_filedir.php.
        list($file->contenthash, $file->filesize, $newfile) = $filesystem->add_file_from_string($tofilecontent);

        if (empty($file->filesize)) {
            mtrace("Filesize is empty.");
            // Error -> Remove new file if possible to cleanup workingdirectories!
            $this->cleanup_working_directories($file->contenthash, $fromfilepath, $tofilepath);
            // Throw error to revert possible db changes.
            throw new coding_exception('filehasnofilesize');
        }

        if ((CLI_SCRIPT && !$this->calledbytask) || !empty($this->dryrun)) {
            mtrace("The file " . $file->filename . " was optimized:\n
    Old vs. New Contenthash: " . $fileold->contenthash . " vs " . $file->contenthash . "\n
    Old vs. New Filesize: " . $fileold->filesize . " vs " . $file->filesize
            . " => -" . round((1 - ($file->filesize / $fileold->filesize)) * 100, 2) . "%\n\n");
        }

        if (!empty($this->dryrun)) {
            // Remove new file if possible to cleanup workingdirectories!
            $this->cleanup_working_directories($file->contenthash, $fromfilepath, $tofilepath);
            // Delte record from tool_imageoptimize_files table.
            $DB->set_field('tool_imageoptimize_files', 'contenthashnew', '', ['contenthashold' => $file->contenthash]);
            $DB->set_field('tool_imageoptimize_files', 'timeprocessed', '', ['contenthashold' => $file->contenthash]);
            $DB->set_field('tool_imageoptimize_files', 'filesize', '', ['contenthashold' => $file->contenthash]);
            return true;
        }

        $this->update_fileinfo($fileold, $file);

        // Remove tempfile.
        @unlink($fromfilepath);
        // Remove tempprocessed file.
        @unlink($tofilepath);

        // After all... Delete the old file.
        // BUT only delete the file by contenthash, if the contenthashes are different.
        // Otherwise you will delete a needed file.
        // This can happen, if an optimized file is (multiple times) optimized again.
        if ($fileold->contenthash !== $file->contenthash) {
            $fulldirold = $filesystemhelper->get_fulldir_from_hash_imgopt($fileold->contenthash);
            $fileold = $fulldirold . "/" . $fileold->contenthash;
            @unlink($fileold);
        }
        return true;
    }

    /**
     * Cleanup because of error or dryrun.
     * @param string $contenthash
     * @param string $tempfilepathfrom
     * @param string $tempfilepathto
     */
    private function cleanup_working_directories($contenthash, $tempfilepathfrom, $tempfilepathto) :void {
        $filesystemhelper = file_system_filedir_helper::get_instance();
        $fulldirnew = $filesystemhelper->get_fulldir_from_hash_imgopt($contenthash);
        $filenew = $fulldirnew . "/" . $contenthash;
        @unlink($filenew);
        @unlink($tempfilepathfrom);
        @unlink($tempfilepathto);
    }

    /**
     * Temporary file path from input file name
     * @param object $file
     * @return string
     */
    private function get_temp_file_path($file) : string {
        return sys_get_temp_dir() . '/' . random_string() . '.' . $this->get_file_extension($file);
    }

    /**
     * Get an extension by the file record mime type
     * @param object $file
     * @return string
     */
    protected function get_file_extension($file) : string {
        if (isset(self::FILES_TYPES[$file->mimetype])
            && !empty(self::FILES_TYPES[$file->mimetype])) {
            return self::FILES_TYPES[$file->mimetype];
        }
        return '';
    }

    /**
     * Insert fileinfo to tool_imageoptimize_files if necessary.
     * @param object $tempfileobject
     */
    protected function insert_fileinfo($tempfileobject) {
        global $DB;
        if (!$DB->record_exists('tool_imageoptimize_files', ['fileid' => $tempfileobject->fileid])) {
            $DB->insert_record('tool_imageoptimize_files', $tempfileobject);
        }
    }

    /**
     * Update file tables.
     * @param object $fileold from files table
     * @param object $filenew from files table
     */
    protected function update_fileinfo($fileold, $filenew) : void {
        global $DB;

        $relatedreferences = $DB->get_records('files', ['contenthash' => $fileold->contenthash]);

        foreach ($relatedreferences as $fileobject) {
            // Update files table.
            $fileobject->contenthash = $filenew->contenthash;
            $fileobject->filesize = $filenew->filesize;
            $DB->update_record('files', $fileobject);

            // Update tool_imageoptimize_files table.
            $fileoptimizeobject = $DB->get_record('tool_imageoptimize_files', ['fileid' => $fileobject->id]);
            if (!empty($fileoptimizeobject)) {
                $fileoptimizeobject->contenthashnew = $filenew->contenthash;
                $fileoptimizeobject->filesize = $filenew->filesize;
                $fileoptimizeobject->timeprocessed = time();
                $DB->update_record('tool_imageoptimize_files', $fileoptimizeobject);
            } else {
                $fileoptimizeobject = new \stdClass();
                $fileoptimizeobject->contenthashold = $fileold->contenthash;
                $fileoptimizeobject->fileid = $fileobject->id;
                $fileoptimizeobject->filesizeold = $fileold->filesize;
                $fileoptimizeobject->contenthashnew = $filenew->contenthash;
                $fileoptimizeobject->filesize = $filenew->filesize;
                $fileoptimizeobject->timeprocessed = time();
                $this->insert_fileinfo($fileoptimizeobject);
            }
        }
    }

    /**
     * Handels the task execution call.
     */
    public function task_call_optimization() : void {
        $this->calledbytask = true;
        if (!empty($this->config->enablebackgroundoptimizing)) {
            $chunksize = 2000;
            if (!empty($this->config->maxchunksize)) {
                $chunksize = $this->config->maxchunksize;
            }
            $this->process_files($chunksize);
        }
    }

    /**
     * Handels the cli execution call.
     * @param array $options
     */
    public function cli_call_optimization($options) :void {
        if (empty($options['execute'])) {
            $this->dryrun = true;
        } else {
            $this->dryrun = false;
        }

        $this->config->filessortorder = $options['sort'];
        $this->process_files((int)$options['chunksize']);
    }

    /**
     * Insert fileinfo to tool_imageoptimize_files if necessary depending on contenthash.
     * @param object $filerecord
     */
    public function insert_fileinfo_depending_on_contenthash($filerecord) :void {
        global $DB;
        $insertobject = new \stdClass();
        $insertobject->fileid = $filerecord->id;
        $insertobject->contenthashold = $filerecord->contenthash;
        $insertobject->filesizeold = $filerecord->filesize;
        if ($DB->record_exists('tool_imageoptimize_files', ['contenthashnew' => $filerecord->contenthash])) {
            $insertobject->timeprocessed = time();
        }
        $DB->insert_record('tool_imageoptimize_files', $insertobject);
    }

    /**
     * Populate imageoptimize table.
     */
    public function task_call_populate_imageoptimze_table() :void {
        global $DB;

        $mimetypes = array_keys($this::FILES_TYPES);
        list($sqlpart, $params) = $DB->get_in_or_equal($mimetypes);

        $timestart = time();
        $timeend = $timestart;

        // Do the job for one minute.
        while ($timeend - $timestart < 60) {

            $insertfromfileid = get_config('tool_imageoptimize', 'lastprocessedfileid');
            if (empty($insertfromfileid)) {
                $insertfromfileid = 0;
            }

            $select = "id > " . $insertfromfileid . " AND mimetype " . $sqlpart;
            $records = $DB->get_records_select(
                'files',
                $select,
                $params,
                'id ASC',
                'id, contenthash, filesize',
                0,
                intval(get_config('tool_imageoptimize', 'maxchunksizeimport'))
            );

            // Exit the loop, if no more files have to be imported.
            if (empty($records)) {
                break;
            }

            foreach ($records as $record) {
                $insertobject = new \stdClass();
                $insertobject->fileid = $record->id;
                $insertobject->contenthashold = $record->contenthash;
                $insertobject->filesizeold = $record->filesize;
                $conditions = [
                    'contenthashold' => $record->contenthash,
                ];
                if (!$DB->record_exists('tool_imageoptimize_files', $conditions)) {
                    $DB->insert_record('tool_imageoptimize_files', $insertobject);
                }
                $lastprocessedfileid = $record->id;
            }

            // Set the last processed file id once per chunk to minimize write processes.
            set_config('lastprocessedfileid', $lastprocessedfileid, 'tool_imageoptimize');

            $timeend = time();
        }
    }

    /**
     * Cheks if a package command is installed.
     * @param string $name of the package
     * @return bool if package is installed
     */
    public function check_package_command_for_testing(string $name) : bool {
        if (!exec('which ' . $name)) {
            if (!exec('dpkg -s ' . $name)) {
                return false;
            }
        }
        return true;
    }
}