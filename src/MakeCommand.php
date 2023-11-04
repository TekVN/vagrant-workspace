<?php

namespace DNT\Devweb;

use DNT\Devweb\Settings\JsonSettings;
use DNT\Devweb\Settings\YamlSettings;
use DNT\Devweb\Traits\GeneratesSlugs;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    use GeneratesSlugs;

    /**
     * The base path of the Laravel installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The name of the project folder.
     *
     * @var string
     */
    protected $projectName;

    /**
     * Sluggified Project Name.
     *
     * @var string
     */
    protected $defaultProjectName;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->basePath = getcwd();
        $this->projectName = basename($this->basePath);
        $this->defaultProjectName = $this->slug($this->projectName);

        $this
            ->setName('make')
            ->setDescription('Install Devweb into the current project')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'The name of the virtual machine.', $this->defaultProjectName)
            ->addOption('hostname', null, InputOption::VALUE_OPTIONAL, 'The hostname of the virtual machine.', $this->defaultProjectName)
            ->addOption('ip', null, InputOption::VALUE_OPTIONAL, 'The IP address of the virtual machine.')
            ->addOption('no-after', null, InputOption::VALUE_NONE, 'Determines if the after.sh file is not created.')
            ->addOption('no-aliases', null, InputOption::VALUE_NONE, 'Determines if the aliases file is not created.')
            ->addOption('example', null, InputOption::VALUE_NONE, 'Determines if a Devweb example file is created.')
            ->addOption('json', null, InputOption::VALUE_NONE, 'Determines if the Devweb settings file will be in json format.');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->vagrantfileExists()) {
            $this->createVagrantfile();
        }

        if (!$input->getOption('no-aliases') && !$this->aliasesFileExists()) {
            $this->createAliasesFile();
        }

        if (!$input->getOption('no-after') && !$this->afterShellScriptExists()) {
            $this->createAfterShellScript();
        }

        $format = $input->getOption('json') ? 'json' : 'yaml';

        if (!$this->settingsFileExists($format)) {
            $this->createSettingsFile($format, [
                'name' => $input->getOption('name'),
                'hostname' => $input->getOption('hostname'),
                'ip' => $input->getOption('ip'),
            ]);
        }

        if ($input->getOption('example') && !$this->exampleSettingsExists($format)) {
            $this->createExampleSettingsFile($format);
        }

        $this->checkForDuplicateConfigs($output);

        $output->writeln('Devweb Installed!');

        return 0;
    }

    /**
     * Determines if Devweb has been installed "per project".
     *
     * @return bool
     */
    protected function isPerProjectInstallation()
    {
        return (bool) preg_match('/vendor\/dnt\/devweb/', __DIR__);
    }

    /**
     * Determine if the Vagrantfile exists.
     *
     * @return bool
     */
    protected function vagrantfileExists()
    {
        return file_exists("{$this->basePath}/Vagrantfile");
    }

    /**
     * Create a Vagrantfile.
     *
     * @return void
     */
    protected function createVagrantfile()
    {
        copy(__DIR__ . '/../resources/localized/Vagrantfile', "{$this->basePath}/Vagrantfile");
    }

    /**
     * Determine if the aliases file exists.
     *
     * @return bool
     */
    protected function aliasesFileExists()
    {
        return file_exists("{$this->basePath}/aliases");
    }

    /**
     * Create aliases file.
     *
     * @return void
     */
    protected function createAliasesFile()
    {
        if ($this->isPerProjectInstallation()) {
            copy(__DIR__ . '/../resources/localized/aliases', "{$this->basePath}/aliases");
        } else {
            copy(__DIR__ . '/../resources/aliases', "{$this->basePath}/aliases");
        }
    }

    /**
     * Determine if the after shell script exists.
     *
     * @return bool
     */
    protected function afterShellScriptExists()
    {
        return file_exists("{$this->basePath}/after.sh");
    }

    /**
     * Create the after shell script.
     *
     * @return void
     */
    protected function createAfterShellScript()
    {
        copy(__DIR__ . '/../resources/after.sh', "{$this->basePath}/after.sh");
    }

    /**
     * Determine if the settings file exists.
     *
     * @param  string  $format
     * @return bool
     */
    protected function settingsFileExists($format)
    {
        return file_exists("{$this->basePath}/Devweb.{$format}");
    }

    /**
     * Create the settings file.
     *
     * @param  string  $format
     * @param  array  $options
     * @return void
     */
    protected function createSettingsFile($format, $options)
    {
        $SettingsClass = ($format === 'json') ? JsonSettings::class : YamlSettings::class;

        $filename = $this->exampleSettingsExists($format) ?
            "{$this->basePath}/Devweb.{$format}.example" :
            __DIR__ . "/../resources/Devweb.{$format}";

        $settings = $SettingsClass::fromFile($filename);

        if (!$this->exampleSettingsExists($format)) {
            $settings->updateName($options['name'])
                ->updateHostname($options['hostname']);
        }

        $settings->updateIpAddress($options['ip'])
            ->configureSites($this->projectName, $this->defaultProjectName)
            ->configureSharedFolders($this->basePath, $this->defaultProjectName)
            ->save("{$this->basePath}/Devweb.{$format}");
    }

    /**
     * Determine if the example settings file exists.
     *
     * @param  string  $format
     * @return bool
     */
    protected function exampleSettingsExists($format)
    {
        return file_exists("{$this->basePath}/Devweb.{$format}.example");
    }

    /**
     * Create the settings example file.
     *
     * @param  string  $format
     * @return void
     */
    protected function createExampleSettingsFile($format)
    {
        copy("{$this->basePath}/Devweb.{$format}", "{$this->basePath}/Devweb.{$format}.example");
    }

    /**
     * Checks if JSON and Yaml config files exist, if they do
     * the user is warned that Yaml will be used before
     * JSON until Yaml is renamed / removed.
     *
     * @param  OutputInterface  $output
     * @return void
     */
    protected function checkForDuplicateConfigs(OutputInterface $output)
    {
        if (file_exists("{$this->basePath}/Devweb.yaml") && file_exists("{$this->basePath}/Devweb.json")) {
            $output->writeln(
                '<error>WARNING! You have Devweb.yaml AND Devweb.json configuration files</error>'
            );
            $output->writeln(
                '<error>WARNING! Devweb will not use Devweb.json until you rename or delete the Devweb.yaml</error>'
            );
        }
    }
}
