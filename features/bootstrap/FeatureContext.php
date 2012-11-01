<?php
require_once __DIR__ . '/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "Deploi" . DIRECTORY_SEPARATOR . "Deploi.php";

use Behat\Behat\Context\BehatContext;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    public function __construct(array $params)
    {
        \Deploi\Deploi::registerAutoloader();

        $this->useContext("config", new ConfigurationContext($params));
        $this->useContext("archive", new ArchiveContext($params));
    }
}
