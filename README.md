About this project
==================

This project is a fictional situation where a writer named *Jean Forteroche* wants 
to publish his new book online episode by episode without having to use WordPress.
More details can be found at [here][1].

It is entirely made with the [Symfony framework][2] and has successfully passed
[SensioLabsInsight][3] validation. [**Guard authentication**][4] is also implemented.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e8d7fac7-a902-4647-b5a1-1b6d9ccd1a63/mini.png)](https://insight.sensiolabs.com/projects/e8d7fac7-a902-4647-b5a1-1b6d9ccd1a63)

Libraries used
--------------

* jQuery
* Bootstrap
* TinyMCE

Install instructions
--------------------

Clone the repository : `git clone https://github.com/lakchote/blog.git`

*(Type in console @ project's repository )*

Download the dependencies with composer : `composer install`

Create the database : `php bin/console doctrine:database:create`

Create the migration file : `php bin/console doctrine:migrations:diff`

Run the migration : `php bin/console doctrine:migrations:migrate`

Load the fixtures (for the Admin user) : `php bin/console doctrine:fixtures:load`

Modify the absolute URL inside "app/config/services.yml" with your website's URL to enable OAuth2 authentification : `redirectUri: "https://yourWebsiteURL.com/login/facebook/check"`

Run the project : `php bin/console server:run` 

[1]: https://openclassrooms.com/projects/creez-un-blog-pour-un-ecrivain-1
[2]: https://symfony.com/
[3]: https://insight.sensiolabs.com/
[4]: http://symfony.com/doc/current/security/guard_authentication.html
[score]: https://insight.sensiolabs.com/projects/e8d7fac7-a902-4647-b5a1-1b6d9ccd1a63/small.png



