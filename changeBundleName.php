#!/usr/bin/env php
<?php

namespace Installation;

class Screen
{
    const COLOR = "\e[1m\e[1;37m\e[1;44m";
    const RESET = "\e[0m\n";

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
        static::writeLines((array)$line, '', '');
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

    const OK = 'OK';

    const ERROR = 'ERROR';

    public function execute($params)
    {
        if (!$this->checkNumberArguments($params)) {
            return 1;
        }

        $name = $params[1];

        if (!$this->checkBundleName($name)) {
            return 2;
        }

        $this->replaceInFiles($this->listOfReplacements($name));

        $this->renameExtension($name);

        $this->renameExtensionTest($name);

        $this->renameBundle($name);

        $this->renameBundleTest($name);

        if (!$this->updateComposer()) {
            return 3;
        }

        $this->deleteOriginalRepository();

        $this->generateNewComposer();

        $this->remember();

        return true;
    }

    private function checkNumberArguments($params)
    {
        Screen::writeInitStatus(array('Checking number of arguments'));
        if (count($params) === 2) {
            Screen::writeEndStatus(self::OK);
            return true;
        }
        Screen::writeEndStatus(self::ERROR);
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
            Screen::writeEndStatus(self::OK);
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

    private function replaceInFiles($replacements)
    {
        foreach ($replacements as $filename => $data) {
            Screen::writeInitStatus(array($data[0]));
            try {
                $fileContent = file_get_contents($filename);
                $fileContent = str_replace($data[1], $data[2], $fileContent);
                Screen::writeEndStatus(static::OK);
                file_put_contents($filename, $fileContent);
            } catch (\Exception $e) {
                Screen::writeEndStatus(static::ERROR);
            }
        }
    }

    private function listOfReplacements($name)
    {
        $result = array();
        $result['app/config/config.yml'] = array(
            'Replacing app config values',
            array('skilla_base_development'),
            array(str_replace(array('/', '_bundle'), array('_', ''), $this->underscore($name)))
        );

        $nameParts = explode('/', $name);
        $bundle = $nameParts[1];
        $result['app/config/routing.yml'] = array(
            'Replacing Routing values',
            array(
                'base_development_bundle',
                'SkillaBaseDevelopmentBundle'
            ),
            array(
                $this->underscore($bundle),
                str_replace('/', '', $name)
            )
        );

        $nameParts = explode('/', $name);
        $vendor = $nameParts[0];
        $bundle = $nameParts[1];
        $replace = $vendor . '\\' . $bundle . '\\' . $vendor . $bundle;
        $result['app/AppKernel.php'] = array(
            'Replacing appKernel values',
            array('Skilla\BaseDevelopmentBundle\SkillaBaseDevelopmentBundle'),
            array($replace)
        );

        $result['src/phpunit.xml'] = array(
            'Replacing phpunit.xml values',
            array(
                'SkillaBaseDevelopmentBundle',
            ),
            array(
                str_replace('/', '', $name),
            ),
        );

        $result['src/Bundle/DependencyInjection/Configuration.php'] = array(
            'Replacing DependencyInjection/Configuration values',
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
                'skilla_base_development'
            ),
            array(
                $this->backslash($name),
                str_replace(array('/', '_bundle'), array('_', ''), $this->underscore($name))
            )
        );

        $result['src/Bundle/DependencyInjection/SkillaBaseDevelopmentExtension.php'] = array(
            'Replacing DependencyInjection/Extension values',
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
                'SkillaBaseDevelopmentExtension'
            ),
            array(
                $this->backslash($name),
                str_replace(array('/', 'Bundle'), array('', ''), $name) . 'Extension'
            )
        );

        $result['src/Bundle/SkillaBaseDevelopmentBundle.php'] = array(
            'Replacing Bundle values',
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
                'SkillaBaseDevelopmentBundle'
            ),
            array(
                $this->backslash($name),
                str_replace('/', '', $name)
            )
        );

        $result['src/Bundle/Controller/DefaultController.php'] = array(
            'Replacing DefaultController values',
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
            ),
            array(
                $this->backslash($name),
            )
        );

        $result['src/Bundle/Tests/SkillaBaseDevelopmentBundleTest.php'] = array(
            'Replacing DefaultController values',
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
                'SkillaBaseDevelopmentBundle'
            ),
            array(
                $this->backslash($name),
                str_replace('/', '', $name)
            )
        );

        $result['src/Bundle/Tests/Controller/DefaultControllerTest.php'] = array(
            'Replacing DefaultControllerTes values',
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
            ),
            array(
                $this->backslash($name),
            )
        );

        $result['src/Bundle/Tests/DependencyInjection/SkillaBaseDevelopmentExtensionTest.php'] = array(
            'Replacing SkillaBaseDevelopmentExtensionTest values',
            array(
                'SkillaBaseDevelopmentExtension',
                'Skilla\\BaseDevelopmentBundle',
                'skilla_base_development'
            ),
            array(
                str_replace(array('/', 'Bundle'), array('', 'Extension'), $name),
                $this->backslash($name),
                $this->underscore(str_replace(array('/', 'Bundle'), array('', ''), $name))
            )
        );

        $result['src/Bundle/SkillaBaseDevelopmentBundle.php'] = array(
            'Replacing Bundle values',
            array(
                static::SKILLA_BASE_DEVELOPMENT_BUNDLE,
                'SkillaBaseDevelopmentBundle'
            ),
            array(
                $this->backslash($name),
                str_replace('/', '', $name)
            )
        );

        $result['composer.json'] = array(
            'Replacing composer values',
            array(
                'skilla/base-development-bundle',
                'Skilla\\\\BaseDevelopmentBundle'
            ),
            array(
                $this->dash($name),
                preg_quote($this->backslash($name))
            )
        );

        return $result;
    }

    private function renameExtension($name)
    {
        try {
            Screen::writeInitStatus(array('Renaming DependencyInjection/Extension'));
            $oldFilename = __DIR__ . '/src/Bundle/DependencyInjection/SkillaBaseDevelopmentExtension.php';
            $newFilename = __DIR__ . '/src/Bundle/DependencyInjection/' .
                str_replace(
                    array('/', 'Bundle'),
                    array('', ''),
                    $name
                ) .
                'Extension.php';
            rename($oldFilename, $newFilename);
            Screen::writeEndStatus(self::OK);
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus(self::ERROR);
        }
        return false;
    }

    private function renameExtensionTest($name)
    {
        try {
            Screen::writeInitStatus(array('Renaming Tests/DependencyInjection/Extension'));
            $oldFilename = __DIR__ . '/src/Bundle/Tests/DependencyInjection/SkillaBaseDevelopmentExtensionTest.php';
            $newFilename = __DIR__ . '/src/Bundle/Tests/DependencyInjection/' .
                str_replace(
                    array('/', 'Bundle'),
                    array('', ''),
                    $name
                ) .
                'ExtensionTest.php';
            rename($oldFilename, $newFilename);
            Screen::writeEndStatus(self::OK);
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus(self::ERROR);
        }
        return false;
    }

    private function renameBundle($name)
    {
        try {
            Screen::writeInitStatus(array('Renaming bundle'));
            $oldFilename = __DIR__ . '/src/Bundle/SkillaBaseDevelopmentBundle.php';
            $newFilename = __DIR__ . '/src/Bundle/' . str_replace('/', '', $name) . '.php';
            rename($oldFilename, $newFilename);
            Screen::writeEndStatus(self::OK);
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus(self::ERROR);
        }
        return false;
    }

    private function renameBundleTest($name)
    {
        try {
            Screen::writeInitStatus(array('Renaming test bundle'));
            $oldFilename = __DIR__ . '/src/Bundle/Tests/SkillaBaseDevelopmentBundleTest.php';
            $newFilename = __DIR__ . '/src/Bundle/Tests/' . str_replace('/', '', $name) . 'Test.php';
            rename($oldFilename, $newFilename);
            Screen::writeEndStatus(self::OK);
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus(self::ERROR);
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
            Screen::writeEndStatus(self::OK);
            Screen::writeInitStatus('Removing composer.lock');
            exec('rm -rf composer.lock');
            Screen::writeEndStatus(self::OK);
            Screen::writeInitStatus('Clearing composer cache');
            exec($composerName . ' clearcache 1>&2', $output, $return);
            Screen::writeInitStatus('Installing vendors');
            exec($composerName . ' install 1>&2', $output, $return);
            Screen::writeEndStatus(self::OK);
            Screen::writeLN(array('Executing app/console'));
            exec('app/console 1>&2');
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus(self::ERROR);
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
        if (preg_match('~https:\/\/github\.com\/skilla\/BaseDevelopmentBundle~', $content) < 1) {
            Screen::writeEndStatus('ERROR original repository not exists');
            return false;
        }
        try {
            $this->delTree('.git');
            Screen::writeEndStatus(self::OK);
            return true;
        } catch (\Exception $e) {
            Screen::writeEndStatus(self::ERROR);
        }
    }

    private function generateNewComposer()
    {
        $jsonContent = file_get_contents('composer.json');
        $phpContent = json_decode($jsonContent, true);
        unset($phpContent['extra']);
        unset($phpContent['config']);
        unset($phpContent['scripts']);
        unset($phpContent['require-dev']);
        unset($phpContent['autoload']['classmap']);
        $phpContent['description'] = '';
        $phpContent['keywords'] = array('');
        $phpContent['authors'][0]['name'] = '';
        $phpContent['authors'][0]['email'] = '';
        $jsonContent = json_encode($phpContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $jsonContent = str_replace(
            'src/',
            '',
            $jsonContent
        );

        file_put_contents(__DIR__ . '/src/composer.json', $jsonContent);
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
