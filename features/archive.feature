Feature: Archive
    In order to build a deployment payload archive
    As a developer
    I need to be able to define a list of included/excluded file paths to be archived

    Scenario: Adding a number of valid file paths results in a valid archive
        Given I add an array of valid file paths
        When I create an archive
        Then I should have the same number of files in the archive

    Scenario: Adding no file paths results in an empty archive
        Given I add no file paths
        When I create an archive
        Then I should have a non-existent archive
        And I should have received an exception

    Scenario: Adding an invalid file path results in an exception
        Given I add an invalid file path
        When I create an archive
        Then I should have a non-existent archive
        And I should have received an exception
