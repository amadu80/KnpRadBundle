Feature: Configure routes using conventions
    In order to normalize the way routes are created
    As a RAD developer
    I need rad bundle to provide a routing loader

    Scenario: generate common routes
        Given I write in "App/Controller/FooController.php":
        """
        <?php namespace App\Controller {
            class FooController {
                public function indexAction() { return ['object' => get_class($object)]; }
                public function showAction() { return ['object' => $object]; }
            }
        }
        """
        And I write in "App/Resources/config/routing.yml":
        """
        App:Foo:
            defaults:
                _resources: {}
        """
        When I visit "app_foo_index" page
        Then I should see "App:Foo:index.html.twig"


