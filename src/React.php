<?php


namespace Gytree\PHPReact;


class React
{
    private static $_bundles;
    private static $_components_path;

    public static function render(Component $component, string $target): string
    {
        # Todo: agregar parametros a los componentes
        $element = "document.getElementById('$target')";
        $component = "React.createElement(components." . $component->name() . ")";
        return "<script>ReactDOM.render($component, $element);</script>";
    }

    public static function addBundle($path)
    {
        static::$_bundles[] = "<script src=\"$path\"></script>";
    }

    public static function setComponentsPath($path)
    {
        static::$_components_path = $path;
    }

    public static function assets()
    {
        $environment = static::getEnvMode();

        $assets = [
            "<script crossorigin src=\"https://unpkg.com/react@16/umd/react.$environment.min.js\"></script>",
            "<script crossorigin src=\"https://unpkg.com/react-dom@16/umd/react-dom.$environment.min.js\"></script>"
        ];

        $assets = array_merge($assets, static::$_bundles);

        return $assets;
    }

    protected static function getEnvMode()
    {
        $env = strtoupper(getenv("REACT_ENV"));
        switch ($env) {
            case "DEV":
            case "DEVELOPMENT":
                return "development";
            default:
                return "production";
        }
    }
}