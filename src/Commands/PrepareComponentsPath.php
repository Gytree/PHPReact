<?php

namespace Gytree\PHPReact\Commands;

use Gytree\PHPReact\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareComponentsPath extends Command
{
    protected static $defaultName = "components:prepare-path";

    protected function configure()
    {
        $this->addArgument(
            "path",
            InputArgument::REQUIRED,
            "the path where you will add your react components"
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument("path");
        if (!is_dir($path) and !mkdir($path)) {
            $output->writeln("The given path doesn't exists");
            return -1;
        }

        $path = realpath($path);
        if (!$this->prepareBuildPath($path)) {
            $output->writeln("Unable to create the build path.");
            return -1;
        };

        if (!$this->prepareComponentsPath($path)) {
            $output->writeln("Unable to setup the components path");
            return -1;
        }


        $output->writeln(
            "Paths prepared please create your components files inside of the "
                . "components dir"
        );
        return 0;
    }

    protected function prepareBuildPath($root_path): bool
    {
        $build_directory = Commands::getBuildPath($root_path);
        if (is_dir($build_directory)) {
            return true;
        }
        if (!mkdir($build_directory, 0777, true)) {
            return false;
        };
        $cwd = getcwd();
        chdir($build_directory);

        $webpack_config = file_get_contents(Commands::ASSETS_PATH . "/webpack.config.js");
        $webpack_config = str_replace(
            "const work_path = ''",
            "const work_path = '$root_path'",
            $webpack_config
        );
        file_put_contents("webpack.config.js", $webpack_config);
        copy(Commands::ASSETS_PATH . "/package.json", "package.json");

        $system_output = 0;
        system("npm install", $system_output);
        chdir($cwd);
        return $system_output == 0;
    }


    protected function prepareComponentsPath($root_path): bool
    {
        $cwd = getcwd();
        chdir($root_path);

        if (!is_dir("components") and !mkdir("components")) {
            return false;
        }
        if (is_dir("components")) {
            $hello_component = Commands::ASSETS_PATH . DIRECTORY_SEPARATOR . "Hello.jsx";
            return copy($hello_component, "components/Hello.jsx");
        }
        chdir($cwd);
        return false;
    }
}
