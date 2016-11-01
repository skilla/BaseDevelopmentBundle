# BaseDevelopmentBundle

__Skeleton bundle for the development and testing of new bundles for Symfony__

[__Status__]

[![Latest Stable Version](https://img.shields.io/github/release/skilla/BaseDevelopmentBundle.svg)](https://packagist.org/packages/skilla/base-development-bundle#1.0.0)
[![Build Status](https://travis-ci.org/skilla/BaseDevelopmentBundle.svg?branch=master)](https://travis-ci.org/skilla/BaseDevelopmentBundle)
[![Total Downloads](https://poser.pugx.org/skilla/base-development-bundle/downloads)](https://packagist.org/packages/skilla/base-development-bundle)
[![Latest Unstable Version](https://poser.pugx.org/skilla/base-development-bundle/v/unstable)](https://packagist.org/packages/skilla/base-development-bundle#dev-master)
[![License](https://poser.pugx.org/skilla/base-development-bundle/license)](https://packagist.org/packages/skilla/base-development-bundle)
[![composer.lock](https://poser.pugx.org/skilla/base-development-bundle/composerlock)](https://packagist.org/packages/skilla/base-development-bundle)

[__English__]

If you plan to create a bundle for Symfony 2 or 3, this can be a good way to start.

This repository provides you with the basic structure to begin programming without the need to include your bundle on a project and test it from there.

Once cloned just run a script that will update the namespaces, the names of classes and configuration files to suit your "user / bundle".

As use in 5 steps:
- Clone this repository
- Run the script changeBundleName.php to adapt the bundle to its "user / bundle name"
- Initialize our git repository in src/ folder
- Upload the bundle to your repository
- Start programming your project

[__Español__]

Si planeas crear un bundle para symfony 2 o 3, esta puede ser una buena manera de empezar.

Este repositorio le provee de la estructura básica para empezar a programar sin la necesidad de incluir su bundle en un proyecto y testearlo desde ahí.

Una vez clonado solo hay que ejecutar un script que actualizará los namespaces, los nombres de las clases y los ficheros de configuración para adaptarlos a su "user/bundle".

Como usarlo en 5 pasos:
- Clonar este repositorio
- Ejecutar el script changeBundleName.php para adaptár el bundle a su "usuario/nombre del bundle"
- Inicialice su repositorio git en la carpeta src/
- Subir el bundle a su repositorio
- Iniciar la programación de su proyecto


[__EXAMPLE__ | __EJEMPLO__]

```shell
skilla@caribdis $ git clone https://github.com/skilla/BaseDevelopmentBundle.git MyNewBundle
```

Cloning into 'MyNewBundle'...  
remote: Counting objects: 123, done.  
remote: Compressing objects: 100% (69/69), done.  
remote: Total 123 (delta 32), reused 0 (delta 0), pack-reused 53  
Receiving objects: 100% (123/123), 33.07 KiB | 0 bytes/s, done.  
Resolving deltas: 100% (49/49), done.  
Checking connectivity... done.  

```shell
skilla@caribdis $ cd MyNewBundle
skilla@caribdis MyNewBundle$ php changeBundleName.php User/TestBundle
```
 Checking number of arguments ... OK  
 Checking bundle name ... OK  
 Replacing app config values ... OK  
 Replacing appKernel values ... OK  
 Replacing DependencyInjection/Configuration values ... OK  
 Replacing DependencyInjection/Extension values ... OK  
 Renaming DependencyInjection/Extension ... OK  
 Replacing Bundle values ... OK  
 Renaming bundle ... OK  
 Replacing composer values ... OK  
 Updating composer ... ...  Removing vendors ... OK  
 Removing composer.lock ... OK  
 Installing vendors ...  
Loading composer repositories with package information  
Updating dependencies (including require-dev)  
   Installing symfony/polyfill-mbstring (v1.2.0)  
   Loading from cache  

   Installing symfony/translation (v3.1.6)  
   Loading from cache  

   Installing symfony/templating (v3.1.6)  
   Loading from cache  

   Installing symfony/stopwatch (v3.1.6)  
   Loading from cache  

   Installing symfony/polyfill-util (v1.2.0)  
   Loading from cache  

   . . .  
   . . .  
   
   Installing phpunit/phpunit (5.6.2)  
   Loading from cache  

   Installing incenteev/composer-parameter-handler (v2.1.2)  
   Loading from cache  

symfony/security-core suggests installing symfony/expression-language (For using the expression voter)  
symfony/security-core suggests installing symfony/ldap (For using LDAP integration)  
symfony/security-core suggests installing symfony/validator (For using the user password constraint)  
paragonie/random_compat suggests installing ext-libsodium (Provides a modern crypto API that can be used to generate random bytes.)  
symfony/routing suggests installing symfony/expression-language (For using expression matching)  
symfony/http-kernel suggests installing symfony/browser-kit ()  
symfony/http-kernel suggests installing symfony/var-dumper ()  
symfony/dependency-injection suggests installing symfony/expression-language (For using expressions in service container configuration)  
symfony/dependency-injection suggests installing symfony/proxy-manager-bridge (Generate service proxies to lazy load them)  
symfony/class-loader suggests installing symfony/polyfill-apcu (For using ApcClassLoader on HHVM)  
symfony/cache suggests installing symfony/polyfill-apcu (For using ApcuAdapter on HHVM)  
symfony/framework-bundle suggests installing ext-apcu (For best performance of the system caches)  
symfony/framework-bundle suggests installing symfony/form (For using forms)  
symfony/framework-bundle suggests installing symfony/process (For using the server:run, server:start, server:stop, and server:status commands)  
symfony/framework-bundle suggests installing symfony/property-info (For using the property_info service)  
symfony/framework-bundle suggests installing symfony/serializer (For using the serializer service)  
symfony/framework-bundle suggests installing symfony/validator (For using validation)  
symfony/console suggests installing symfony/process ()  
sebastian/global-state suggests installing ext-uopz (*)  
phpunit/phpunit suggests installing phpunit/php-invoker (~1.1)  
Writing lock file  
Generating autoload files  

Incenteev\ParameterHandler\ScriptHandler::buildParameters  
Creating the "app/config/parameters.yml" file  
Some parameters are missing. Please provide them.  
secret ('this is the kernel secret for symfony/framework.'):  
OK

 Executing app/console

Symfony version 3.1.6 - app/dev/debug

Usage:  
  command [options] [arguments]

Options:  
  -h, --help            Display this help message  
  -q, --quiet           Do not output any message  
  -V, --version         Display this application version  
      --ansi            Force ANSI output  
      --no-ansi         Disable ANSI output  
  -n, --no-interaction  Do not ask any interactive question  
  -e, --env=ENV         The Environment name. [default: "dev"]  
      --no-debug        Switches off debug mode.  
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug  

Available commands:  
  help                    Displays help for a command  
  list                    Lists commands  
 assets  
  assets:install          Installs bundles web assets under a public web directory  
 cache  
  cache:clear             Clears the cache  
  cache:warmup            Warms up an empty cache  
 config  
  config:dump-reference   Dumps the default configuration for an extension  
 debug  
  debug:config            Dumps the current configuration for an extension  
  debug:container         Displays current services for an application  
  debug:event-dispatcher  Displays configured listeners for an application  
  debug:translation       Displays translation messages information  
 lint  
  lint:yaml               Lints a file and outputs encountered errors  
 translation  
  translation:update      Updates the translation file  
 Deleting original .git directory ... OK  

 remember change author name and email in composer.json  
 
 Good luck with our new bundle  

```shell
skilla@caribdis MyNewBundle$ cd src
skilla@caribdis src$ git init
```
Initialized empty Git repository in .../MyNewBundle/src/.git/
```shell
skilla@caribdis src$ echo "# MyNewBundle" >> README.md
skilla@caribdis src$ git add .
skilla@caribdis src$ git commit -m "first commit"
```
[master (root-commit) ce3f704] first commit  
 xxx file changed, xxxx insertion(+)  
 create mode 100644 README.md  
 . . .  
 . . .  
```shell
skilla@caribdis src$ git remote add origin https://github.com/skilla/MyNewBundle.git
skilla@caribdis src$ git push -u origin master
```
Counting objects: 3, done.  
Writing objects: 100% (3/3), 235 bytes | 0 bytes/s, done.  
Total 3 (delta 0), reused 0 (delta 0)  
To https://github.com/skilla/MyNewBundle.git  
[new branch]      master -> master  
Branch master set up to track remote branch master from origin.  
