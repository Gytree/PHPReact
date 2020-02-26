<?php


namespace Gytree\PHPReact;


class React
{
    private static $_bundles;
    private static $_components_path;

    public static function render(Component $component, string $target): string
    {
        $element = "document.getElementById('$target')";
        $props = static::getComponentPropsString($component);
        $component = "React.createElement(components." . $component->name() . ", $props)";
        
        return "<script>window.addEventListener(\"load\", function(){ReactDOM.render($component, $element)});</script>";
    }

    protected static function getComponentPropsString(Component $component): string
    {
        $props = "{}";
        if ($component_props = $component->getProps()) {
            return json_encode($component_props);
        }
        return $props;
    }

    public static function addBundle($path)
    {
        static::$_bundles[] = $path;
    }

    public static function setComponentsPath($path)
    {
        static::$_components_path = $path;
    }

    public static function assets()
    {
        $scripts = self::getRequiredScripts();
        return array_map(function ($script) {
            if (substr($script, 0, 4) !== "http") {
                $script = "<script src=\"$script\"></script>";
            } else {
                $script = "<script crossorigin src=\"$script\"></script>";
            }
            return $script;
        }, $scripts);
    }

    public static function getRequiredScripts()
    {
        $environment = static::getEnvMode();
        $scripts = [
            "https://unpkg.com/react@16/umd/react.$environment.min.js",
            "https://unpkg.com/react-dom@16/umd/react-dom.$environment.min.js"
        ];
        return array_merge($scripts, static::$_bundles ?? []);
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
