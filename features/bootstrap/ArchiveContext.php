<?php
use Behat\Behat\Context\BehatContext;
use Deploi\Util\File\FileSet;
use Deploi\Modules\Archive\Archive;
use Behat\Behat\Event\ScenarioEvent;
use Deploi\Config;
use Behat\Behat\Exception\BehaviorException;
use Behat\Behat\Exception\PendingException;

/**
 *
 */
class ArchiveContext extends BehatContext
{
    /**
     * @var \Deploi\Modules\Archive\Archive
     */
    private $archive;

    /**
     * @var \Deploi\Util\File\FileSet
     */
    private $fileSet;

    private $basePath;
    private $archivePath;
    private $exceptionThrown;

    /**
     * @BeforeScenario
     */
    public function prepare(ScenarioEvent $event)
    {
        $this->fileSet = null;
        $this->archive = null;

        $this->basePath        = dirname(dirname(__DIR__));
        $this->archivePath     = "";
        $this->exceptionThrown = false;
    }

    /**
     * @AfterScenario
     */
    public function dispose(ScenarioEvent $event)
    {
        if (file_exists($this->archivePath)) {
            @unlink($this->archivePath);
        }
    }

    /**
     * @Given /^I add an array of valid file paths$/
     */
    public function iAddAnArrayOfValidFilePaths()
    {
        $this->fileSet = new FileSet($this->basePath, array("composer.json", ".travis.yml", "bin"));
        $this->archive = new Archive();
        $this->archive->setFileSet($this->fileSet);
    }

    /**
     * @When /^I create an archive$/
     */
    public function iCreateAnArchive()
    {
        try {
            $this->archivePath = $this->archive->save(sys_get_temp_dir(), true, false);
        } catch(Exception $e) {
            $this->exceptionThrown = true;
        }
    }

    /**
     * @Then /^I should have the same number of files in the archive$/
     */
    public function iShouldHaveTheSameNumberOfFilesInTheArchive()
    {
        $file = new PharData($this->archivePath);
        assertEquals(count($this->archive->getFileSet()->getPaths()), $file->count());
    }

    /**
     * @Given /^I add no file paths$/
     */
    public function iAddNoFilePaths()
    {
        $this->fileSet = new FileSet();
        $this->archive = new Archive();
        $this->archive->setFileSet($this->fileSet);
    }

    /**
     * @Then /^I should have a non-existent archive$/
     */
    public function iShouldHaveANonExistentArchive()
    {
        assertTrue(empty($this->archivePath) || $this->exceptionThrown);
    }

    /**
     * @Given /^I add an invalid file path$/
     */
    public function iAddAnInvalidFilePath()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should get an exception$/
     */
    public function iShouldGetAnException()
    {
        throw new PendingException();
    }
}
