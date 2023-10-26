<?php

namespace Tests;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use DNT\Devweb\MakeCommand;
use DNT\Devweb\Traits\GeneratesSlugs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;
use Tests\Traits\GeneratesTestDirectory;

class MakeCommandTest extends TestCase
{
    use ArraySubsetAsserts, GeneratesSlugs, GeneratesTestDirectory;

    /** @test */
    public function it_displays_a_success_message()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertStringContainsString('Devweb Installed!', $tester->getDisplay());
    }

    /** @test */
    public function it_returns_a_success_status_code()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertEquals(0, $tester->getStatusCode());
    }

    /** @test */
    public function a_vagrantfile_is_created_if_it_does_not_exists()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Vagrantfile');

        $this->assertFileEquals(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Vagrantfile',
            __DIR__ . '/../resources/localized/Vagrantfile'
        );
    }

    /** @test */
    public function an_existing_vagrantfile_is_not_overwritten()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Vagrantfile',
            'Already existing Vagrantfile'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertStringEqualsFile(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Vagrantfile',
            'Already existing Vagrantfile'
        );
    }

    /** @test */
    public function an_aliases_file_is_created_by_default()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases');

        $this->assertFileEquals(
            __DIR__ . '/../resources/aliases',
            self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases'
        );
    }

    /** @test */
    public function a_localized_aliases_file_is_created_by_default_in_per_project_installations()
    {
        $this->markTestSkipped('Currently unable to emulate a per project installation');

        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases');

        $this->assertFileEquals(
            __DIR__ . '/../resources/localized/aliases',
            self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases'
        );
    }

    /** @test */
    public function an_existing_aliases_file_is_not_overwritten()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases',
            'Already existing aliases'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases');

        $this->assertStringEqualsFile(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases',
            'Already existing aliases'
        );
    }

    /** @test */
    public function an_aliases_file_is_not_created_if_it_is_explicitly_told_to()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--no-aliases' => true,
        ]);

        $this->assertFileDoesNotExist(self::$testDirectory . DIRECTORY_SEPARATOR . 'aliases');
    }

    /** @test */
    public function an_after_shell_script_is_created_by_default()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'after.sh');

        $this->assertFileEquals(
            __DIR__ . '/../resources/after.sh',
            self::$testDirectory . DIRECTORY_SEPARATOR . 'after.sh'
        );
    }

    /** @test */
    public function an_existing_after_shell_script_is_not_overwritten()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'after.sh',
            'Already existing after.sh'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'after.sh');

        $this->assertStringEqualsFile(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'after.sh',
            'Already existing after.sh'
        );
    }

    /** @test */
    public function an_after_file_is_not_created_if_it_is_explicitly_told_to()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--no-after' => true,
        ]);

        $this->assertFileDoesNotExist(self::$testDirectory . DIRECTORY_SEPARATOR . 'after.sh');
    }

    /** @test */
    public function an_example_yaml_settings_is_created_if_requested()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--example' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml.example');
    }

    /** @test */
    public function an_existing_example_yaml_settings_is_not_overwritten()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml.example',
            'name: Already existing Devweb.yaml.example'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--example' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml.example');

        $this->assertStringEqualsFile(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml.example',
            'name: Already existing Devweb.yaml.example'
        );
    }

    /** @test */
    public function an_example_json_settings_is_created_if_requested()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--example' => true,
            '--json' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json.example');
    }

    /** @test */
    public function an_existing_example_json_settings_is_not_overwritten()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json.example',
            '{"name": "Already existing Devweb.json.example"}'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--example' => true,
            '--json' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json.example');

        $this->assertStringEqualsFile(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json.example',
            '{"name": "Already existing Devweb.json.example"}'
        );
    }

    /** @test */
    public function a_yaml_settings_is_created_if_it_is_does_not_exists()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml');
    }

    /** @test */
    public function an_existing_yaml_settings_is_not_overwritten()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml',
            'name: Already existing Devweb.yaml'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertStringEqualsFile(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml',
            'name: Already existing Devweb.yaml'
        );
    }

    /** @test */
    public function a_json_settings_is_created_if_it_is_requested_and_it_does_not_exists()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--json' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json');
    }

    /** @test */
    public function an_existing_json_settings_is_not_overwritten()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json',
            '{"message": "Already existing Devweb.json"}'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertStringEqualsFile(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json',
            '{"message": "Already existing Devweb.json"}'
        );
    }

    /** @test */
    public function a_yaml_settings_is_created_from_a_yaml_example_if_it_exists()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml.example',
            "message: 'Already existing Devweb.yaml.example'"
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml');

        $this->assertStringContainsString(
            "message: 'Already existing Devweb.yaml.example'",
            file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml')
        );
    }

    /** @test */
    public function a_yaml_settings_created_from_a_yaml_example_can_override_the_ip_address()
    {
        copy(
            __DIR__ . '/../resources/Devweb.yaml',
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml.example'
        );

        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--ip' => '192.168.10.11',
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml');

        $settings = Yaml::parse(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml'));

        $this->assertEquals('192.168.10.11', $settings['ip']);
    }

    /** @test */
    public function a_json_settings_is_created_from_a_json_example_if_is_requested_and_if_it_exists()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json.example',
            '{"message": "Already existing Devweb.json.example"}'
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--json' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json');

        $this->assertStringContainsString(
            '"message": "Already existing Devweb.json.example"',
            file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json')
        );
    }

    /** @test */
    public function ajson_settings_created_from_a_json_example_can_override_the_ip_address()
    {
        copy(
            __DIR__ . '/../resources/Devweb.json',
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json.example'
        );

        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--json' => true,
            '--ip' => '192.168.10.11',
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json');

        $settings = json_decode(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json'), true);

        $this->assertEquals('192.168.10.11', $settings['ip']);
    }

    /** @test */
    public function a_yaml_settings_can_be_created_with_some_command_options_overrides()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--name' => 'test_name',
            '--hostname' => 'test_hostname',
            '--ip' => '127.0.0.1',
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml');

        $settings = Yaml::parse(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml'));

        self::assertArraySubset([
            'name' => 'test_name',
            'hostname' => 'test_hostname',
            'ip' => '127.0.0.1',
        ], $settings);
    }

    /** @test */
    public function a_json_settings_can_be_created_with_some_command_options_overrides()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--json' => true,
            '--name' => 'test_name',
            '--hostname' => 'test_hostname',
            '--ip' => '127.0.0.1',
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json');

        $settings = json_decode(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json'), true);

        self::assertArraySubset([
            'name' => 'test_name',
            'hostname' => 'test_hostname',
            'ip' => '127.0.0.1',
        ], $settings);
    }

    /** @test */
    public function a_yaml_settings_has_preconfigured_sites()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml');

        $settings = Yaml::parse(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml'));

        $this->assertEquals([
            'map' => 'devweb.test',
            'to' => '/var/www/laravel/public',
        ], $settings['sites'][0]);
    }

    /** @test */
    public function a_json_settings_has_preconfigured_sites()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--json' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json');

        $settings = json_decode(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json'), true);

        $this->assertEquals([
            'map' => 'devweb.test',
            'to' => '/var/www/laravel/public',
        ], $settings['sites'][0]);
    }

    /** @test */
    public function a_yaml_settings_has_preconfigured_shared_folders()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml');

        $projectDirectory = basename(getcwd());
        $projectName = $this->slug($projectDirectory);
        $settings = Yaml::parse(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml'));

        // The "map" is not tested for equality because getcwd() (The method to obtain the project path)
        // returns a directory in a different location that the test directory itself.
        //
        // Example:
        //  - project directory: /private/folders/...
        //  - test directory: /var/folders/...
        //
        // The curious thing is that both directories point to the same location.
        //
        $this->assertMatchesRegularExpression("/{$projectDirectory}/", $settings['folders'][0]['map']);
        $this->assertEquals('/var/www', $settings['folders'][0]['to']);
        $this->assertEquals($projectName, $settings['name']);
        $this->assertEquals($projectName, $settings['hostname']);
    }

    /** @test */
    public function a_json_settings_has_preconfigured_shared_folders()
    {
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([
            '--json' => true,
        ]);

        $this->assertFileExists(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json');

        $projectDirectory = basename(getcwd());
        $projectName = $this->slug($projectDirectory);
        $settings = json_decode(file_get_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json'), true);

        // The "map" is not tested for equality because getcwd() (The method to obtain the project path)
        // returns a directory in a different location that the test directory itself.
        //
        // Example:
        //  - project directory: /private/folders/...
        //  - test directory: /var/folders/...
        //
        // The curious thing is that both directories point to the same location.
        //
        $this->assertMatchesRegularExpression("/{$projectDirectory}/", $settings['folders'][0]['map']);
        $this->assertEquals('/var/www', $settings['folders'][0]['to']);
        $this->assertEquals($projectName, $settings['name']);
        $this->assertEquals($projectName, $settings['hostname']);
    }

    /** @test */
    public function a_warning_is_thrown_if_the_settings_json_and_yaml_exists_at_the_same_time()
    {
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.json',
            '{"message": "Already existing Devweb.json"}'
        );
        file_put_contents(
            self::$testDirectory . DIRECTORY_SEPARATOR . 'Devweb.yaml',
            "message: 'Already existing Devweb.yaml'"
        );
        $tester = new CommandTester(new MakeCommand());

        $tester->execute([]);

        $this->assertStringContainsString('WARNING! You have Devweb.yaml AND Devweb.json configuration files', $tester->getDisplay());
    }
}
