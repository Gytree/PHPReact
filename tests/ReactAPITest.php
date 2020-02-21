<?php

use Gytree\PHPReact\React;


class ReactAPITest extends \PHPUnit\Framework\TestCase
{
    public function testReactAssetsMode()
    {
        $assets = React::assets();
        $this->assertIsArray($assets);
        $expected_assets_array = [
            "<script crossorigin src=\"https://unpkg.com/react@16/umd/react.production.min.js\"></script>",
            "<script crossorigin src=\"https://unpkg.com/react-dom@16/umd/react-dom.production.min.js\"></script>"
        ];
        $this->assertEquals($expected_assets_array, $assets);
        putenv("REACT_ENV=dev");

        $expected_assets_array = [
            "<script crossorigin src=\"https://unpkg.com/react@16/umd/react.development.min.js\"></script>",
            "<script crossorigin src=\"https://unpkg.com/react-dom@16/umd/react-dom.development.min.js\"></script>"
        ];
        $this->assertEquals($expected_assets_array, React::assets());
    }
}