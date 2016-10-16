#!/usr/bin/env php
<?php
    CheckNumberArguments($argv) || die(1);

    $name = $argv[1];

    CheckBundleName($name) || die(2);

    //ReplaceAppConfig($name);

    //ReplaceAppKernel($name);

    //ReplaceConfiguration($name);

    //ReplaceExtension($name);

    //RenameExtension($name);

    //ReplaceBundle($name);

    //RenameBundle($name);

    //ReplaceComposer($name);

    //UpdateComposer() || die(3);

    Remember();

    function CheckNumberArguments($params) {
        if (count($params)===2) {
            return true;
        }
        echo "use:\n";
        echo "  " . $params[0] . " MyVendor/NewNameBundle\n";
        return false;
    }

    function CheckBundleName($name) {
        $pattern = '~^[A-Z][a-zA-Z]+/[A-Z][a-zA-Z]+Bundle$~';
        if (preg_match($pattern, $name) === 1) {
            return true;
        }
        echo "Bad name '$name'\n";
        echo "  examples:\n";
        echo "    FOS/UserBundle\n";
        echo "    Symfony/FrameworkBundle\n";
        echo "    Skilla/BaseDevelopmentBundle\n";
        return false;
    }

    function ReplaceAppConfig($name) {
        $filename = 'app/config/config.yml';
        $replace = str_replace(array('/', '_bundle'), array('_', ''), underscore($name));
        $content = str_replace(
            'skilla_base_development',
            $replace,
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    function ReplaceAppKernel($name) {
        $filename = 'app/AppKernel.php';
        $nameParts = explode('/', $name);
        $vendor = $nameParts[0];
        $bundle = $nameParts[1];

        $replace = $vendor . '\\' . $bundle . '\\' . $vendor . $bundle;
        $content = str_replace(
            'Skilla\BaseDevelopmentBundle\SkillaBaseDevelopmentBundle',
            $replace,
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    function ReplaceConfiguration($name) {
        $filename = 'Bundle/DependencyInjection/Configuration.php';
        $replace = str_replace(array('/', '_bundle'), array('_', ''), underscore($name));
        $content = str_replace(
            array('Skilla\\BaseDevelopmentBundle', 'skilla_base_development'),
            array(backslash($name), $replace),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    function ReplaceExtension($name) {
        $filename = 'Bundle/DependencyInjection/SkillaBaseDevelopmentExtension.php';
        $replace = str_replace(array('/', 'Bundle'), array('', ''), $name) . 'Extension';
        $content = str_replace(
            array('Skilla\\BaseDevelopmentBundle', 'SkillaBaseDevelopmentExtension'),
            array(backslash($name), $replace),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    function RenameExtension($name) {
        $oldFilename = __DIR__ . '/Bundle/DependencyInjection/SkillaBaseDevelopmentExtension.php';
        $newFilename = __DIR__ . '/Bundle/DependencyInjection/' . str_replace(array('/', 'Bundle'), array('', ''), $name) . 'Extension.php';
        rename($oldFilename, $newFilename);
    }

    function ReplaceBundle($name) {
        $filename = 'Bundle/SkillaBaseDevelopmentBundle.php';
        $replace = str_replace('/', '', $name);
        $content = str_replace(
            array('Skilla\\BaseDevelopmentBundle', 'SkillaBaseDevelopmentBundle'),
            array(backslash($name), $replace),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    function ReplaceComposer($name) {
        $filename = 'composer.json';
        $content = str_replace(
            'skilla/base-development-bundle',
            dash($name),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    function RenameBundle($name) {
        $oldFilename = __DIR__ . '/Bundle/SkillaBaseDevelopmentBundle.php';
        $newFilename = __DIR__ . '/Bundle/' . str_replace('/', '', $name) . '.php';
        rename($oldFilename, $newFilename);
    }

    function underscore($name) {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), str_replace('_', '.', $name)));
    }

    function dash($name) {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1-\\2', '\\1-\\2'), str_replace('-', '.', $name)));
    }

    function backslash($name) {
        return str_replace('/', '\\', $name);
    }

    function UpdateComposer() {
        $color = "\e[1m\e[1;37m\e[1;44m\n\n";
        $reset = "\n\e[0m\n";
        $composerNames = array('composer', 'composer.phar', './composer', './composer.phar');
        $output = '';
        $return = 0;
        foreach ($composerNames as $composerName) {
            exec('which '.$composerName, $output, $return);
            if ($return === 0) {
                break;
            }
        }
        if ($return !== 0) {
            echo implode(' or ', $composerNames) . ' not found or not is executable';
            return false;
        }

        echo "$color Removing vendors ...$reset";
        exec('rm -rf vendor/*');
        echo "$color Removing composer.lock ...$reset";
        exec('rm -rf composer.lock');
        echo "$color Installing vendors ...$reset";
        exec($composerName . ' install 1>&2', $output, $return);
        echo "$color Executing app/console ...$reset";
        exec('app/console 1>&2');
        return true;
    }

    function Remember() {
        $color = "\e[1m\e[1;37m\e[1;44m\n\n";
        $reset = "\n\e[0m\n";
        echo $color;
        echo " Remember change author name and email in composer.json\n";
        echo "\n";
        echo " Goog luck with the bundle\n";
        echo $reset;
    }
