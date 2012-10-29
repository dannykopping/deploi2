<?php
use Behat\Behat\Context\BehatContext;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    public function __construct(array $params)
    {
        $this->useContext("config", new ConfigurationContext($params));
    }
}
