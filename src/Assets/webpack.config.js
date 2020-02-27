const fs = require("fs");
const path = require("path");
const webpack = require("webpack");

const settings = JSON.parse(fs.readFileSync("settings.json"));

var output_path = '.';
if (settings.hasOwnProperty("output_path")) {
    output_path = settings["output_path"];
}

module.exports = [
    "source-map"
].map(() => ({
    entry: "./index.js",
    mode: "development",
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
    plugins: [new webpack.HotModuleReplacementPlugin()],
    externals: [
        /^react.+$/
    ]
}))