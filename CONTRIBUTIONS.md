# How contribute to this project?

## Good practice for the remote repository
### GitHub
At first, you need to get and install ToDoAndCo project on your local machine. 
To install it follow the instructions in the file README.md
Once the project is up and running, you need to create your development branch 
enter the following commands in the console
   ```bash
   git branch your_branch_name
   
   git checkout your_branch_name
   
   git push -u origin your_branch_name
   ```
Before starting development, you need to create an issue to explain the changes you intend to make to the application
Once your work finished you need to push your update on the remote repository:
   ```bash
   git push -u origin your_branch_name
   ```

### Tests
we want the application to be robust and avoid new changes modifying existing behaviour, so
we need test our application and maintain over 70% coverage
to launch the tests already in place
   ```bash
    symfony php bin/phpunit
   ```  
to generate cover information in html format at the same time
   ```bash
    symfony php bin/phpunit --coverage-html coverage-test
   ``` 
to create html coverage info
### Check and correct your code
 - we use PHP code sniffer to format the code correctly
   ```bash
   ./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix
   ``` 
 - To static analyze from level 1 to 7
   ```bash
   ./vendor/bin/phpstan analyse src --level 7
   ``` 
### Finally
Then you can create a pull request, with all the issues linked to this update to request authorisation merge 
your branch on the master branch
## Translations
Above all, we don't add hard-coded text to the application. You must use Symfony's translation system
To bound the translations use the yaml file in translations directory : messages.{locale}.yaml
example in your PHP file:
   ```php
    $translator->trans('app.user.not_found')
   ```
   ```php
    {{ 'app.user.not_found'|trans }}
   ```   
   ```yaml
    app:
        user:
            not_found: Sorry, this user doesn't exists
   ```
more info: https://symfony.com/doc/current/translation.html
## New access control
Use the voting system in the repository Security/Voter, create your rules and conditions and add 
isGranted method to the controlled route
   ```php
    $this->isGranted('YOUR_RULE_NAME', $this->neededObject())
   ```  
more info: https://symfony.com/doc/current/security/voters.html

## Others good practices
you must respect the naming convention:
examples
 - Controller in PascalCase and add Controller at the end MyControllerController
 - Twig file use SnakeCase my_template.html.twig
Test your migration:
 - Check if master is update
   ```bash
    git checkout master && git fetch && git pull && git checkout - && git rebase master
   ``` 
 - test migrations
   ```bash
    git checkout master
    php bin/console doctrine:database:drop --force --no-interaction
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:create --no-interaction
    php bin/console doctrine:migrations:sync-metadata-storage --no-interaction
    php bin/console doctrine:migrations:version --add --all
    php bin/console doctrine:fixtures:load --no-interaction --group=dev
    git checkout -
    php bin/console doctrine:migrations:migrate
   ``` 
