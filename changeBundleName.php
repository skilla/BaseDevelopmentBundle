#!/usr/bin/env php
<?php

namespace Installation;

class Screen
{
    const COLOR = "\e[1m\e[1;37m\e[1;44m\n\n";
    const RESET = "\n\e[0m\n";

    private static $isStarted = false;

    public static function writeLN(array $lines)
    {
        static::startWrite();
        static::writeLines($lines, ' ', PHP_EOL);
        static::endWrite();
    }

    public static function writeInitStatus($line)
    {
        static::startWrite();
        static::writeLines((array)$line, ' ', ' ... ');
    }

    public static function writeEndStatus($line)
    {
        static::writeLines((array)$line, '', PHP_EOL);
        static::endWrite();
    }

    private static function startWrite()
    {
        if (!static::$isStarted) {
            echo static::COLOR;
        }
        static::$isStarted = true;
    }

    private static function writeLines(array $lines, $tabulator = ' ', $newLine = PHP_EOL)
    {
        foreach ($lines as $line) {
            echo $tabulator . $line . $newLine;
        }
    }

    private static function endWrite()
    {
        if (static::$isStarted) {
            echo static::RESET;
        }
        static::$isStarted = false;
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
        Screen::writeInitStatus(array('Checking number of arguments'));
        if (count($params) === 2) {
            Screen::writeEndStatus('OK');
            return true;
        }
        Screen::writeEndStatus('ERROR');
        Screen::writeLN(array(
            "use:",
            "  " . $params[0] . " MyVendor/NewNameBundle"
        ));
        return false;
    }

    private function checkBundleName($name)
    {
        Screen::writeInitStatus(array('Checking bundle name'));
        $pattern = '~^[A-Z][a-zA-Z]+/[A-Z][a-zA-Z]+Bundle$~';
        if (preg_match($pattern, $name) === 1) {
            Screen::writeEndStatus('OK');
            return true;
        }
        Screen::writeLN(array(
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
        Screen::writeInitStatus(array('Replacing app config values'));
        try {
            $filename = 'app/config/config.yml';
            $replace = str_replace(array('/', '_bundle'), array('_', ''), $this->underscore($name));
            $content = str_replace(
                'skilla_base_development',
                $replace,
                file_get_contents($filename)
            );
            file_put_contents($filename, $content);
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function replaceAppKernel($name)
    {
        try {
            Screen::writeInitStatus(array('Replacing appKernel values'));
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
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function replaceConfiguration($name)
    {
        try {
            Screen::writeInitStatus(array('Replacing DependencyInjection/Configuration values'));
            $filename = 'Bundle/DependencyInjection/Configuration.php';
            $replace = str_replace(array('/', '_bundle'), array('_', ''), $this->underscore($name));
            $content = str_replace(
                array(static::SKILLA_BASE_DEVELOPMENT_BUNDLE, 'skilla_base_development'),
                array($this->backslash($name), $replace),
                file_get_contents($filename)
            );
            file_put_contents($filename, $content);
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function replaceExtension($name)
    {
        try {
            Screen::writeInitStatus(array('Replacing DependencyInjection/Extension values'));
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
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function renameExtension($name)
    {
        try {
            Screen::writeInitStatus(array('Renaming DependencyInjection/Extension'));
            $oldFilename = __DIR__ . '/Bundle/DependencyInjection/SkillaBaseDevelopmentExtension.php';
            $newFilename = __DIR__ . '/Bundle/DependencyInjection/' .
                str_replace(
                    array('/', 'Bundle'),
                    array('', ''),
                    $name
                ) .
                'Extension.php';
            rename($oldFilename, $newFilename);
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function replaceBundle($name)
    {
        try {
            Screen::writeInitStatus(array('Replacing Bundle values'));
            $filename = 'Bundle/SkillaBaseDevelopmentBundle.php';
            $replace = str_replace('/', '', $name);
            $content = str_replace(
                array(static::SKILLA_BASE_DEVELOPMENT_BUNDLE, 'SkillaBaseDevelopmentBundle'),
                array($this->backslash($name), $replace),
                file_get_contents($filename)
            );
            file_put_contents($filename, $content);
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function replaceComposer($name)
    {
        try {
            Screen::writeInitStatus(array('Replacing composer values'));
            $filename = 'composer.json';
            $content = str_replace(
                'skilla/base-development-bundle',
                $this->dash($name),
                file_get_contents($filename)
            );
            $content = str_replace(
                'Skilla\\\\BaseDevelopmentBundle',
                preg_quote($this->backslash($name)),
                $content
            );
            file_put_contents($filename, $content);
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function renameBundle($name)
    {
        try {
            Screen::writeInitStatus(array('Renaming bundle'));
            $oldFilename = __DIR__ . '/Bundle/SkillaBaseDevelopmentBundle.php';
            $newFilename = __DIR__ . '/Bundle/' . str_replace('/', '', $name) . '.php';
            rename($oldFilename, $newFilename);
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
        return false;
    }

    private function updateComposer()
    {
        Screen::writeInitStatus(array('Updating composer ...'));
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
        try {
            Screen::writeInitStatus('Removing vendors');
            exec('rm -rf vendor/*');
            Screen::writeEndStatus('OK');
            Screen::writeInitStatus('Removing composer.lock');
            exec('rm -rf composer.lock');
            Screen::writeEndStatus('OK');
            Screen::writeInitStatus('Installing vendors');
            exec($composerName . ' install 1>&2', $output, $return);
            Screen::writeEndStatus('OK');
            Screen::writeLN(array('Executing app/console'));
            exec('app/console 1>&2');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
    }

    private function deleteOriginalRepository()
    {
        Screen::writeInitStatus('Deleting original .git directory');
        if (!is_dir('.git') || !is_readable('.git/config')) {
            Screen::writeEndStatus('ERROR .git/ not exists or is not readable');
            return false;
        }
        $content = file_get_contents('.git/config');
        if (preg_match('~https://github.com/skilla/BaseDevelopmentBundle.git~', $content) < 1) {
            Screen::writeEndStatus('ERROR original repositori not exists');
            return false;
        }
        try {
            $this->delTree('.git');
            Screen::writeEndStatus('OK');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus('ERROR');
        }
    }

    private function remember()
    {
        Screen::writeLN(array(
            "remember change author name and email in composer.json",
            "",
            "Good luck with our new bundle",
        ));
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

    private function delTree($directoryName)
    {
        $files = array_diff(scandir($directoryName), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$directoryName/$file")) ? $this->delTree("$directoryName/$file") : unlink("$directoryName/$file");
        }
        return rmdir($directoryName);
    }
}

$installation = new Installation();
$installation->execute($argv);
