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
 * Unit Test for image optimizer task.
 *
 * @package    tool_imageoptimize
 * @copyright  2021 ISB Bayern
 * @author     Peter Mayer, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
use \tool_imageoptimize\tool_image_optimize_helper;
use \tool_imageoptimize\file_system_filedir_helper;
/**
 * Unit Test for image optimizer task.
 *
 * @package    tool_imageoptimize
 * @copyright  2021 ISB Bayern
 * @author     Peter Mayer, peter.mayer@isb.bayern.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class imageoptimize_task_test extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        global $CFG, $DB;

        $this->resetAfterTest();

        $DB->delete_records('files');

        // Enable background optimization.
        set_config('enablebackgroundoptimizing', true, 'tool_imageoptimize');
        set_config('jpg_enabled', true, 'tool_imageoptimize');
        set_config('png_enabled', true, 'tool_imageoptimize');
        set_config('gif_enabled', true, 'tool_imageoptimize');

        $filepath = $CFG->dirroot . '/admin/tool/imageoptimize/tests/fixtures/koala.jpg';
        $storedfile[] = $this->create_stored_file_from_path($filepath);

        $filepath = $CFG->dirroot . '/admin/tool/imageoptimize/tests/fixtures/penguins.jpg';
        $storedfile[] = $this->create_stored_file_from_path($filepath);

        $filepath = $CFG->dirroot . '/admin/tool/imageoptimize/tests/fixtures/source.gif';
        $storedfile[] = $this->create_stored_file_from_path($filepath);
    }

    /**
     * Convenience to take a fixture test file and create a stored_file.
     *
     * @param string $filepath
     * @return stored_file
     */
    protected function create_stored_file_from_path($filepath) {
        $syscontext = context_system::instance();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'tool_imageoptimize',
            'filearea'  => 'unittest' . rand(),
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => basename($filepath)
        );

        $fs = get_file_storage();
        return $fs->create_file_from_pathname($filerecord, $filepath);
    }

    /**
     * Test cases for optimizing file by task.
     */
    public function test_optimizing_image_desc_order() {
        global $DB;
        ob_start();

        $filesystemhelper = file_system_filedir_helper::get_instance();

        // Check if the files are in files table.
        list($insql, $params) = $DB->get_in_or_equal(['koala.jpg', 'penguins.jpg', 'source.gif']);
        $select = "filename " . $insql;
        $setupfiles = $DB->get_records_select('files', $select, $params);
        $this->assertCount(3, $setupfiles);

        // Check if the files are physically existing in filestorage.
        foreach ($setupfiles as $setupfile) {
            $fulldirold = $filesystemhelper->get_fulldir_from_hash_imgopt($setupfile->contenthash);
            $fileold = $fulldirold . "/" . $setupfile->contenthash;
            $this->assertFileExists($fileold);
        }

        // Set filesortorder to newest files first.
        set_config('filessortorder', 'iddesc', 'tool_imageoptimize');

        // Set maxchunksize.
        set_config('maxchunksize', 3, 'tool_imageoptimize');

        // Process file.
        $imageoptimizehelper = tool_image_optimize_helper::get_instance();
        $imageoptimizehelper->process_files(get_config('tool_imageoptimize', 'maxchunksize'));

        // Check that there are three files in tool_imageoptimize_files table.
        $imgoptfiles = $DB->get_records('tool_imageoptimize_files');
        $this->assertCount(3, $imgoptfiles);

        foreach ($imgoptfiles as $imgoptfile) {
            // Check if new file exists.
            $processedfile = $DB->get_record('files', ['id' => $imgoptfile->fileid]);
            $fulldirnew = $filesystemhelper->get_fulldir_from_hash_imgopt($processedfile->contenthash);
            $filenew = $fulldirnew . "/" . $processedfile->contenthash;
            $this->assertFileExists($filenew);

            // Check if old file does not exist any more.
            $fulldirold = $filesystemhelper->get_fulldir_from_hash_imgopt($imgoptfile->contenthashold);
            $fileold = $fulldirold . "/" . $imgoptfile->contenthashold;
            $this->assertFileNotExists($fileold);

            // Check if filesizenew is set in tool_imageoptimize_files and is equal to the filesize of the physical file.
            $this->assertEquals($imgoptfile->filesize, filesize($filenew));

            // Check if the contenthash is set correctly to the tool_imageoptimize_files table.
            $this->assertEquals($imgoptfile->contenthashnew, $processedfile->contenthash);
        }
        ob_end_clean();
    }

    /**
     * Test cases for optimizing file by task.
     */
    public function test_optimizing_image_asc_order() {
        global $DB;
        ob_start();

        $filesystemhelper = file_system_filedir_helper::get_instance();

        // Set filesortorder to oldest files first.
        set_config('filessortorder', 'idasc', 'tool_imageoptimize');

        // Set maxchunksize.
        set_config('maxchunksize', 3, 'tool_imageoptimize');

        // Process file.
        $imageoptimizehelper = tool_image_optimize_helper::get_instance();
        $imageoptimizehelper->process_files(get_config('tool_imageoptimize', 'maxchunksize'));

        // Check that there are three files in tool_imageoptimize_files table.
        $imgoptfiles = $DB->get_records('tool_imageoptimize_files');
        $this->assertCount(3, $imgoptfiles);

        foreach ($imgoptfiles as $imgoptfile) {
            // Check if new file exists.
            $processedfile = $DB->get_record('files', ['id' => $imgoptfile->fileid]);
            $fulldirnew = $filesystemhelper->get_fulldir_from_hash_imgopt($processedfile->contenthash);
            $filenew = $fulldirnew . "/" . $processedfile->contenthash;
            $this->assertFileExists($filenew);

            // Check if old file does not exist any more.
            $fulldirold = $filesystemhelper->get_fulldir_from_hash_imgopt($imgoptfile->contenthashold);
            $fileold = $fulldirold . "/" . $imgoptfile->contenthashold;
            $this->assertFileNotExists($fileold);

            // Check if filesizenew is set in tool_imageoptimize_files and is equal to the filesize of the physical file.
            $this->assertEquals($imgoptfile->filesize, filesize($filenew));

            // Check if the contenthash is set correctly to the tool_imageoptimize_files table.
            $this->assertEquals($imgoptfile->contenthashnew, $processedfile->contenthash);
        }
        ob_end_clean();
    }

    /**
     * Test cases for handling missing files.
     */
    public function test_handle_missing_files() {
        global $DB;

        ob_start();
        $filesystemhelper = file_system_filedir_helper::get_instance();

        // Check if the files are in files table.
        list($insql, $params) = $DB->get_in_or_equal(['koala.jpg', 'penguins.jpg', 'source.gif']);
        $select = "filename " . $insql;
        $setupfiles = $DB->get_records_select('files', $select, $params);
        $this->assertCount(3, $setupfiles);

        // Make sure that tool_imageoptimize_files table is empty.
        $DB->delete_records('tool_imageoptimize_files');

        // Remove one file physically and let it stay in files table.
        $missingfile = array_shift($setupfiles);
        $fulldirmissingfile = $filesystemhelper->get_fulldir_from_hash_imgopt($missingfile->contenthash);
        unlink($fulldirmissingfile . "/" . $missingfile->contenthash);

        $this->assertFileNotExists($fulldirmissingfile . "/" . $missingfile->contenthash);

        // Set filesortorder to oldest files first.
        set_config('filessortorder', 'iddesc', 'tool_imageoptimize');

        // Set maxchunksize.
        set_config('maxchunksize', 3, 'tool_imageoptimize');

        // Process file.
        $imageoptimizehelper = tool_image_optimize_helper::get_instance();
        $imageoptimizehelper->process_files(get_config('tool_imageoptimize', 'maxchunksize'));

        // Check that there are three files in tool_imageoptimize_files table.
        $imgoptfiles = $DB->get_records('tool_imageoptimize_files');
        $this->assertCount(3, $imgoptfiles);

        // Check that there is a missing file.
        $imgoptfiles = $DB->get_records('tool_imageoptimize_files', ['filenotfound' => 1]);
        $this->assertCount(1, $imgoptfiles);
        foreach ($imgoptfiles as $imgoptfile) {
            $this->assertEquals($missingfile->id, $imgoptfile->fileid);
        }

        ob_end_clean();
    }

    /**
     * Test cases for cli script.
     */
    public function test_cli_script_usage() {
        global $DB;

        ob_start();
        $filesystemhelper = file_system_filedir_helper::get_instance();

        // Check if the files are in files table.
        list($insql, $params) = $DB->get_in_or_equal(['koala.jpg', 'penguins.jpg', 'source.gif']);
        $select = "filename " . $insql;
        $setupfiles = $DB->get_records_select('files', $select, $params);
        $this->assertCount(3, $setupfiles);

        // Make sure that tool_imageoptimize_files table is empty.
        $DB->delete_records('tool_imageoptimize_files');

        // Remove one file physically and let it stay in files table.
        $missingfile = array_shift($setupfiles);
        $fulldirmissingfile = $filesystemhelper->get_fulldir_from_hash_imgopt($missingfile->contenthash);
        unlink($fulldirmissingfile . "/" . $missingfile->contenthash);

        $this->assertFileNotExists($fulldirmissingfile . "/" . $missingfile->contenthash);

        // Set filesortorder to oldest files first.
        set_config('filessortorder', 'iddesc', 'tool_imageoptimize');

        // Set maxchunksize.
        set_config('maxchunksize', 3, 'tool_imageoptimize');

        // Test dryrun.
        $imageoptimizehelper = tool_image_optimize_helper::get_instance();

        $options = [
            'chunksize' => 3,
            'execute' => '',
            'sort' => 'iddesc'
        ];

        // Call cli.
        $imageoptimizehelper->cli_call_optimization($options);

        // Check that there is no file in tool_imageoptimize_files table that can be processed.
        $imgoptfiles = $DB->get_records('tool_imageoptimize_files');

        $this->assertCount(0, $imgoptfiles);

        $options = [
            'chunksize' => 3,
            'execute' => true,
            'sort' => 'iddesc'
        ];

        // Call cli.
        $imageoptimizehelper->cli_call_optimization($options);

        // Check that there are processed files in tool_imageoptimize_files.
        // Here it is not important to compare the whole files. There are other tests that do a comparsion.
        $this->assertCount(3, $DB->get_records('tool_imageoptimize_files'));
        $this->assertCount(2, $DB->get_records('tool_imageoptimize_files', ['filenotfound' => 0]));
        $this->assertCount(1, $DB->get_records('tool_imageoptimize_files', ['filenotfound' => 1]));

        ob_end_clean();
    }

    /**
     * Test if the optimization works if the contenthash does not change.
     */
    public function test_unchanged_contenthash() {
        global $DB;

        ob_start();
        $filesystemhelper = file_system_filedir_helper::get_instance();
        $imageoptimizehelper = tool_image_optimize_helper::get_instance();

        // Make sure that tool_imageoptimize_files table is empty.
        $DB->delete_records('tool_imageoptimize_files');

        // Set filesortorder to oldest files first.
        set_config('filessortorder', 'iddesc', 'tool_imageoptimize');

        // Set maxchunksize.
        set_config('maxchunksize', 1, 'tool_imageoptimize');

        // Test live mode.
        $imageoptimizehelper->dryrun = false;

        // Process file several times to get a filesize and contenthash that won't change any more.
        for ($i = 0; $i <= 5; $i++) {
            $imageoptimizehelper->process_files(get_config('tool_imageoptimize', 'maxchunksize'));
            $DB->delete_records('tool_imageoptimize_files');
        }

        // Check that the file still exits.
        // This will check if the delete statement is skiped for unchanged contenthashes.
        $setupfiles = $DB->get_records('files', ['filename' => 'koala.jpg']);
        $this->assertCount(1, $setupfiles);
        foreach ($setupfiles as $setupfile) {
            $fulldirfile = $filesystemhelper->get_fulldir_from_hash_imgopt($setupfile->contenthash);
            $this->assertFileExists($fulldirfile . "/" . $setupfile->contenthash);
        }

        // Optimize File.
        $imageoptimizehelper->process_files(get_config('tool_imageoptimize', 'maxchunksize'));

        // Check that there is still a file.
        // This will check if the delete statement is skiped for unchanged contenthashes.
        $setupfiles = $DB->get_records('files', ['filename' => 'koala.jpg']);
        $this->assertCount(1, $setupfiles);
        foreach ($setupfiles as $setupfile) {
            $fulldirfile = $filesystemhelper->get_fulldir_from_hash_imgopt($setupfile->contenthash);
            $this->assertFileExists($fulldirfile . "/" . $setupfile->contenthash);
        }
        $this->assertCount(1, $DB->get_records('tool_imageoptimize_files'));

        ob_end_clean();
    }

    /**
     * Test for multiple references to one file.
     */
    public function test_multiple_references_to_a_file() {
        global $CFG, $DB;
        ob_start();

        // Cleanup the files table.
        $DB->delete_records('files');

        // Store some more references to the koala.jpg file.
        $filepath = $CFG->dirroot . '/admin/tool/imageoptimize/tests/fixtures/koala.jpg';
        $contenthashes = [];
        for ($i = 0; $i < 5; $i++) {
            $storedfile = $this->create_stored_file_from_path($filepath);
            $contenthashes[] = $storedfile->get_contenthash();
        }

        // Check that all referenes have the same contenthash.
        $contenthashes = array_unique($contenthashes);
        $this->assertCount(1, $contenthashes);

        $imageoptimizehelper = tool_image_optimize_helper::get_instance();
        // Check that the sql query delivers only one file.
        $filestoprocess = $imageoptimizehelper->get_files_to_process();
        $this->assertCount(1, $filestoprocess);

        // Set maxchunksize.
        set_config('maxchunksize', 1, 'tool_imageoptimize');

        // Process file.
        $imageoptimizehelper->process_files(get_config('tool_imageoptimize', 'maxchunksize'));

        // Each reference should be present in tool_imageoptimize_files table.
        $optimizefileobjects = $DB->get_records('tool_imageoptimize_files');
        $this->assertCount(5, $optimizefileobjects);

        $optimizefileobject = array_shift($optimizefileobjects);

        // Check that there are no more references with the old contenthash.
        $files = $DB->get_records('files', ['contenthash' => $contenthashes[0]]);
        $this->assertCount(0, $files);

        // Check that there are five references with the new contenthash.
        $files = $DB->get_records('files', ['contenthash' => $optimizefileobject->contenthashnew]);
        $this->assertCount(5, $files);

        // Check that there are five references with the new contenthash.
        $files = $DB->get_records('tool_imageoptimize_files', ['contenthashnew' => $optimizefileobject->contenthashnew]);
        $this->assertCount(5, $files);

        ob_end_clean();
    }
}