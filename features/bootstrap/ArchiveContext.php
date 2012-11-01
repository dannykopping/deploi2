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
        try {
            PharData::unlinkArchive($this->archivePath);
        } catch(Exception $e) {
        }

        clearstatcache();
    }

    /**
     * @Given /^I add an array of valid file paths$/
     */
    public function iAddAnArrayOfValidFilePaths()
    {
        $this->fileSet = new FileSet($this->basePath, array("composer.json", ".travis.yml", "bin"));
    }

    /**
     * @Given /^I add an array of path exclusions$/
     */
    public function iAddAnArrayOfPathExclusions()
    {
        $this->fileSet->setExclusions(array("bin/behat", "composer.json"));
    }

    /**
     * @When /^I create an archive$/
     */
    public function iCreateAnArchive()
    {
        $this->archive = new Archive();
        $this->archive->setFileSet($this->fileSet);

        try {
            $this->archivePath = $this->archive->save("archives", false, true);
        } catch(Exception $e) {
            $this->exceptionThrown = true;
        }
    }

    /**
     * @Then /^I should have the correct number of files in the archive$/
     */
    public function iShouldHaveTheCorrectNumberOfFilesInTheArchive()
    {
        assertFileExists($this->archivePath);

        $file = new PharData($this->archivePath);
        assertEquals(count($this->fileSet->getValidPaths()), $file->count());
    }

    /**
     * @Given /^I add no file paths$/
     */
    public function iAddNoFilePaths()
    {
        $this->fileSet = new FileSet();
    }

    /**
     * @Then /^I should have a non-existent archive$/
     */
    public function iShouldHaveANonExistentArchive()
    {
        assertTrue(empty($this->archivePath));
    }

    /**
     * @Given /^I should have received an exception$/
     */
    public function iShouldHaveReceivedAnException()
    {
        assertTrue($this->exceptionThrown);
    }

    /**
     * @Given /^I add an invalid file path$/
     */
    public function iAddAnInvalidFilePath()
    {
        $this->fileSet = new FileSet(null, array("composer.yml"));
    }
}
