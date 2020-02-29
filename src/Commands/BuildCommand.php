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

    protected $work_path;
    protected $build_path;
    protected $components_path;

    protected $output;

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
        $this->output = $output;
        $this->work_path  = $input->getArgument("path");
        if (!is_dir($this->work_path) and !mkdir($this->work_path)) {
            $output->writeln("Unable resolve the work path");
            return -1;
        }
        $this->work_path = realpath($this->work_path);
        if (!$this->prepareBuildPath()) {
            $output->writeln("Unable to prepare the build path");
            return -1;
        }

        $this->setupBuildSettings();
        $this->components_path = $this->work_path . DIRECTORY_SEPARATOR . "components";

        if (!$this->buildComponentsIndex()) {
            $output->writeln("Unable to build the components index");
            return -1;
        }

        if (!$this->makeBuild()) {
            $output->writeln("Unable to make the components build");
            return -1;
        }
        $output->writeln("build done");
        return 0;
    }

    protected function prepareBuildPath(): bool
    {
        $home = $_SERVER["HOME"];
        $hash = md5($this->work_path);
        $path = [$home, ".cache", "phpr", $hash];
        $this->build_path = implode(DIRECTORY_SEPARATOR, $path);

        if (is_dir($this->build_path)) {
            return true;
        }

        if (!mkdir($this->build_path, 0777, true)) {
            return false;
        }

        $cwd = getcwd();
        chdir($this->build_path);

        copy(Commands::ASSETS_PATH . "webpack.config.js", "webpack.config.js");
        copy(Commands::ASSETS_PATH . "package.json", "package.json");
        $output = 0;
        system("npm install", $output);

        chdir($cwd);
        return $output === 0;
    }

    protected function buildComponentsIndex(): bool
    {
        if (!$this->prepareComponentsPath()) {
            return false;
        }
        $files = array_diff(scandir($this->components_path), [".", "..", "index.js"]);
        $content = "";
        $exports = "";
        foreach ($files as $file_name) {
            $ext = substr($file_name, strrpos($file_name, "."));
            if (!in_array($ext, [".js", ".jsx"])) {
                continue;
            }
            $component_name = substr($file_name, 0, strrpos($file_name, "."));
            $component_path =  $this->components_path . DIRECTORY_SEPARATOR . $component_name;
            $content .= "import $component_name from '$component_path';";
            $exports .= (empty($exports) ? "" : ",") . $component_name;
        }
        $content .= "\nexport { $exports };";
        $index_path = $this->build_path . DIRECTORY_SEPARATOR . "index.js";
        return file_put_contents($index_path, $content);
    }

    protected function prepareComponentsPath(): bool
    {
        $this->components_path = $this->work_path . DIRECTORY_SEPARATOR . "components";
        if (!is_dir($this->components_path)) {
            if (!mkdir($this->components_path)) {
                return false;
            }
            $hello_component = Commands::ASSETS_PATH . DIRECTORY_SEPARATOR . "Hello.jsx";
            copy($hello_component, $this->components_path . DIRECTORY_SEPARATOR . "Hello.jsx");
        }
        return true;
    }

    protected function setupBuildSettings(): void
    {
        $settings = ["output_path" => $this->work_path];
        $settings_path = $this->work_path . DIRECTORY_SEPARATOR . "phpr.json";
        if (is_file($settings_path)) {
            $decoded = json_decode(file_get_contents($settings_path), true);
            if (key_exists("output_path", $decoded)) {

                $path = $decoded["output_path"];
                $path = realpath(implode(DIRECTORY_SEPARATOR, [$this->work_path, $path]));
                if ($path === false) {
                    $this->output->writeln(
                        "Unable to resolve the setting output_path: "
                            . $decoded["output_path"] . "\nmake sure that the path"
                            . " exists and you have write permissions"
                    );
                } else if (is_dir($path)) {
                    $settings["output_path"] = $path;
                }
            }
        }
        $settings_file = $this->build_path . DIRECTORY_SEPARATOR . "settings.json";
        file_put_contents($settings_file, json_encode($settings));
    }

    protected function makeBuild(): bool
    {
        if (!is_dir($this->build_path)) {
            return false;
        }
        $cwd = getcwd();
        chdir($this->build_path);
        $output = 0;
        system("npm run build", $output);
        chdir($cwd);
        return $output === 0;
    }
}
