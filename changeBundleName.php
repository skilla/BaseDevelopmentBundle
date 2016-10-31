#!/usr/bin/env php
<?php

namespace Installation;

class Screen
{
    const COLOR = "\e[1m\e[1;37m\e[1;44m\n\n";
    const RESET = "\n\e[0m\n";

    public static function write(array $lines)
    {
        echo static::COLOR;
        foreach ($lines as $line) {
            echo ' ' . $line . PHP_EOL;
        }
        echo static::RESET;
    }
}

class Installation
{
    const SKILLA_BASE_DEVELOPMENT_BUNDLE = 'Skilla\\BaseDevelopmentBundle';

    public function execute($params)
    {
        if (!$this->checkNumberArguments($params)) {
            return 1;
        }

        $name = $params[1];

        if (!$this->checkBundleName($name)) {
            return 2;
        }

        $this->replaceAppConfig($name);

        $this->replaceAppKernel($name);

        $this->replaceConfiguration($name);

        $this->replaceExtension($name);

        $this->renameExtension($name);

        $this->replaceBundle($name);

        $this->renameBundle($name);

        $this->replaceComposer($name);

        if (!$this->updateComposer()) {
            return 3;
        }

        $this->deleteOriginalRepository();

        $this->remember();

        return true;
    }

    private function checkNumberArguments($params)
    {
        if (count($params) === 2) {
            return true;
        }
        Screen::write(array(
            "use:",
            "  " . $params[0] . " MyVendor/NewNameBundle"
        ));
        return false;
    }

    private function checkBundleName($name)
    {
        $pattern = '~^[A-Z][a-zA-Z]+/[A-Z][a-zA-Z]+Bundle$~';
        if (preg_match($pattern, $name) === 1) {
            return true;
        }
        Screen::write(array(
            "Bad name '$name'",
            "  examples:",
            "    FOS/UserBundle",
            "    Symfony/FrameworkBundle",
            "    Skilla/BaseDevelopmentBundle",
        ));
        return false;
    }

    private function replaceAppConfig($name)
    {
        $filename = 'app/config/config.yml';
        $replace = str_replace(array('/', '_bundle'), array('_', ''), $this->underscore($name));
        $content = str_replace(
            'skilla_base_development',
            $replace,
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    private function replaceAppKernel($name)
    {
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

    private function replaceConfiguration($name)
    {
        $filename = 'Bundle/DependencyInjection/Configuration.php';
        $replace = str_replace(array('/', '_bundle'), array('_', ''), $this->underscore($name));
        $content = str_replace(
            array(static::SKILLA_BASE_DEVELOPMENT_BUNDLE, 'skilla_base_development'),
            array($this->backslash($name), $replace),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    private function replaceExtension($name)
    {
        $filename = 'Bundle/DependencyInjection/SkillaBaseDevelopmentExtension.php';
        $replace = str_replace(array('/', 'Bundle'), array('', ''), $name) . 'Extension';
        $content = str_replace(
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
                'SkillaBaseDevelopmentExtension'
            ),
            array(
                $this->backslash($name),
                $replace
            ),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    private function renameExtension($name)
    {
        $oldFilename = __DIR__ . '/Bundle/DependencyInjection/SkillaBaseDevelopmentExtension.php';
        $newFilename = __DIR__ . '/Bundle/DependencyInjection/' .
            str_replace(
                array('/', 'Bundle'),
                array('', ''),
                $name
            ) .
            'Extension.php';
        rename($oldFilename, $newFilename);
    }

    private function replaceBundle($name)
    {
        $filename = 'Bundle/SkillaBaseDevelopmentBundle.php';
        $replace = str_replace('/', '', $name);
        $content = str_replace(
            array(static::SKILLA_BASE_DEVELOPMENT_BUNDLE, 'SkillaBaseDevelopmentBundle'),
            array($this->backslash($name), $replace),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    private function replaceComposer($name)
    {
        $filename = 'composer.json';
        $content = str_replace(
            'skilla/base-development-bundle',
            $this->dash($name),
            file_get_contents($filename)
        );
        file_put_contents($filename, $content);
    }

    private function renameBundle($name)
    {
        $oldFilename = __DIR__ . '/Bundle/SkillaBaseDevelopmentBundle.php';
        $newFilename = __DIR__ . '/Bundle/' . str_replace('/', '', $name) . '.php';
        rename($oldFilename, $newFilename);
    }

    private function underscore($name)
    {
        return strtolower(
            preg_replace(
                array(
                    '/([A-Z]+)([A-Z][a-z])/',
                    '/([a-z\d])([A-Z])/'
                ),
                array(
                    '\\1_\\2', '\\1_\\2'
                ),
                str_replace('_', '.', $name)
            )
        );
    }

    private function dash($name)
    {
        return strtolower(
            preg_replace(
                array(
                    '/([A-Z]+)([A-Z][a-z])/',
                    '/([a-z\d])([A-Z])/'
                ),
                array(
                    '\\1-\\2', '\\1-\\2'
                ),
                str_replace('-', '.', $name)
            )
        );
    }

    private function backslash($name)
    {
        return str_replace('/', '\\', $name);
    }

    private function updateComposer()
    {
        $composerNames = array('composer', 'composer.phar', './composer', './composer.phar');
        $output = '';
        $return = 0;
        $composerName = '';
        foreach ($composerNames as $composerName) {
            exec('which ' . $composerName, $output, $return);
            if ($return === 0) {
                break;
            }
        }
        if ($return !== 0) {
            echo implode(' or ', $composerNames) . ' not found or not is executable';
            return false;
        }

        Screen::write(array("Removing vendors ..."));
        exec('rm -rf vendor/*');
        Screen::write(array("Removing composer.lock ..."));
        exec('rm -rf composer.lock');
        Screen::write(array("Installing vendors ..."));
        exec($composerName . ' install 1>&2', $output, $return);
        Screen::write(array("Executing app/console ..."));
        exec('app/console 1>&2');
        return true;
    }

    private function deleteOriginalRepository()
    {
        if (!is_dir('.git') || !is_readable('.git/config')) {
            return false;
        }
        $content = file_get_contents('.git/config');
        if (preg_match('~https://github.com/skilla/BaseDevelopmentBundle.git~', $content) < 1) {
            return false;
        }
        $this->delTree('.git');
        return true;
    }

    private function remember()
    {
        Screen::write(array(
            "remember change author name and email in composer.json",
            "",
            "Good luck with the bundle",
        ));
    }

    private function delTree($directoryName)
    {
        $files = array_diff(scandir($directoryName), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$directoryName/$file")) ? $this->delTree("$directoryName/$file") : unlink("$directoryName/$file");
        }
        return rmdir($directoryName);
    }
}
