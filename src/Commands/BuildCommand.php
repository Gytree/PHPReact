<?php

namespace Gytree\PHPReact\Commands;

use Gytree\PHPReact\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends Command
{
    protected static $defaultName = "components:build";

    protected function configure()
    {
        $this->addArgument(
            "path",
            InputArgument::REQUIRED,
            "the path where you will add your react components"
        );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path = realpath($input->getArgument("path"));
        if (empty($path)) {
            $output->writeln("Unable to find the given path");
            return -1;
        }
        $components_path = realpath($path . DIRECTORY_SEPARATOR . "components");
        if (!is_dir($components_path)) {
            $output->writeln("Unable to find the components dir");
            return -1;
        }
        $build_path = Commands::getBuildPath($path);
        echo $build_path;
        if (!is_dir($build_path)) {
            $output->writeln("Unable to find te build package path");
            return -1;
        }
        if (!$this->buildComponentsIndex($components_path, $build_path)) {
            $output->writeln("Unable to build the components index");
            return -1;
        }

        chdir($build_path);
        $system_out = null;
        system("npm run build", $system_out);
        $output->writeln(
            $system_out == 0 ? "build done." : "the components build fail"
        );
        return $system_out;
    }

    protected function buildComponentsIndex($components_path, $phpreact_path): bool
    {
        $files = array_diff(scandir($components_path), [".", "..", "index.js"]);
        $content = "";
        $exports = "";
        foreach ($files as $file_name) {
            $component_name = substr($file_name, 0, strrpos($file_name, "."));
            $component_path =  $components_path . "/" . $component_name;
            $content .= "import $component_name from '$component_path';";
            $exports .= (empty($exports) ? "" : ",") . $component_name;
        }
        $content .= "\nexport { $exports };";
        $index_path = $phpreact_path . DIRECTORY_SEPARATOR . "index.js";
        return file_put_contents($index_path, $content);
    }
}
