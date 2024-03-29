<?php
namespace Deploi\Modules\Archive;

use Deploi\Modules\Base;
use Deploi\Util\File\FileSet;
use Phar;
use PharData;
use SplFileInfo;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

/**
 *  Archive Module
 *
 *  Accepts an array of paths and exclusions to turn into an archive
 *
 * @author Danny Kopping
 */
class Archive extends Base
{
    /**
     * @var \Deploi\Util\File\FileSet
     */
    private $fileSet;

    public function register()
    {
        $this->name        = "archive";
        $this->description = "Creates an archive for a set of files";

        $this->hooks = array(
            "pre.archive",
            "post.archive"
        );
    }

    /**
     * Create an archive with a given FileSet
     *
     * @throws \Exception
     */
    public function __construct(FileSet $fileSet = null)
    {
        parent::__construct();

        if (!empty($fileSet)) {
            $this->fileSet = $fileSet;
        }
    }

    /**
     * @param $fileSet
     */
    public function setFileSet($fileSet)
    {
        $this->fileSet = $fileSet;
    }

    /**
     * @return \Deploi\Util\File\FileSet
     */
    public function getFileSet()
    {
        return $this->fileSet;
    }

    /**
     * Create a compress archive with the given FileSet
     *
     * @param      $location
     * @param bool $timestamp
     * @param bool $overwrite
     *
     * @throws \Exception
     * @return string
     */
    public function save($location, $timestamp = true, $overwrite = false)
    {
        $filepath = $this->getFilename($location, $timestamp, $overwrite);

        $tar        = new PharData($filepath);
        $validPaths = $this->fileSet->getValidPaths();

        if (!empty($validPaths) && count($validPaths) > 0) {
            foreach ($validPaths as $path) {
                $tar->addFile($path["path"], $path["relative"]);
            }
        } else {
            throw new Exception("No files to deploy");
        }

        try {
            $tar->convertToData(Phar::TAR, Phar::GZ);
        } catch(Exception $e) {
            // ignore this error
            if (!$overwrite && strpos($e->getMessage(), "a phar with that name already exists") === false) {
                throw $e;
            }
        }

        return realpath($filepath);
    }

    /**
     * Get the archive destination filename
     *
     * @param      $location
     * @param bool $timestamp
     * @param bool $overwrite
     *
     * @return string
     * @throws \Exception
     */
    private function getFilename($location, $timestamp = true, $overwrite = false)
    {
        if (!realpath($location)) {
            @mkdir($location, 0777, true);
        }

        $location = realpath($location);

        if (empty($location)) {
            throw new Exception("Base path invalid for archive");
        }

        $filename = "archive.tar.gz";
        if ($overwrite) {
            $filename = "archive.tar.gz";
        }

        if ($timestamp) {
            $filename = "archive" . date("U") . ".tar.gz";
        }

        return is_file($location)
            ? dirname($location) . DIRECTORY_SEPARATOR . $filename
            : $location . DIRECTORY_SEPARATOR . $filename;
    }
}
