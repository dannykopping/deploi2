Feature: Config options
    In order to use deploi effectively
    As a developer
    I need to be able to set and get configuration options

    Scenario: Configuration file exists
        Given I have a folder called "config"
        And I have a file called "deploi.ini"
        When I try get a list of configuration options
        Then I should not get an exception
