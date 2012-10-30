<?php
require_once __DIR__.'/../../vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';

use Behat\Behat\Context\BehatContext;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    public function __construct(array $params)
    {
        $this->useContext("config", new ConfigurationContext($params));
        $this->useContext("archive", new ArchiveContext($params));
    }
}
