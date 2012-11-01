<?php
use Behat\Behat\Context\BehatContext;
use Deploi\Config;
use Behat\Behat\Exception\BehaviorException;
use Behat\Behat\Exception\PendingException;

/**
 *
 */
class ConfigurationContext extends BehatContext
{
    private $basePath;
    private $folderPath;
    private $filePath;

    public function __construct(array $parameters)
    {
        $this->basePath = realpath(dirname(dirname(__DIR__)));
    }

    /**
     * @Given /^I have a folder called "([^"]*)"$/
     */
    public function iHaveAFolderCalled($folder)
    {
        $this->folderPath = $this->basePath . DIRECTORY_SEPARATOR . $folder;

        if (!file_exists($this->folderPath)) {
            throw new BehaviorException(sprintf("Folder '%s' does not exist", $folder));
        }
    }

    /**
     * @Given /^I have a file called "([^"]*)"$/
     */
    public function iHaveAFileCalled($file)
    {
        $this->filePath = $this->folderPath . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($this->filePath)) {
            throw new BehaviorException(sprintf("File '%s' does not exist", $file));
        }
    }

    /**
     * @When /^I try get a list of configuration options$/
     */
    public function iTryGetAListOfConfigurationOptions()
    {
        Config::setConfigPath($this->folderPath);

        Config::getAll();
    }

    /**
     * @Then /^I should not get an exception$/
     */
    public function iShouldNotGetAnException()
    {
        $options = Config::getAll();

        assertGreaterThan(0, count($options), "Number of configuration options");
    }
}
