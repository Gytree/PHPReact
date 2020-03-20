const fs = require("fs");
const path = require("path");
const webpack = require("webpack");

const settings = JSON.parse(fs.readFileSync("settings.json"));

var mode = settings.mode || "production";
var output_path = settings.output || ".";

module.exports = function (env, argv) {
    return {
        mode: mode,
        devtool: mode === "production" ? false : "source-map",
        entry: "./index.js",
        module: {
            rules: [
                {
                    test: /\.jsx?$/,
                    exclude: /(node_modules|bower_components)/,
                    loader: "babel-loader",
                    options: {presets: ["@babel/env", "@babel/preset-react"]}
                },
                {
                    test: /\.css$/,
                    use: ["style-loader", "css-loader"]
                }
            ]
        },
        resolve: {extensions: ["*", ".js", ".jsx"]},
        output: {
            path: path.resolve(output_path),
            publicPath: output_path,
            filename: "components.js",
            library: "components",
            libraryTarget: "umd",
        },
        plugins: [],
        externals: [
            /^react.+$/
        ]
    };
};