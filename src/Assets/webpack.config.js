const path = require("path");
const webpack = require("webpack");

const work_path = '';

module.exports = [
    "source-map"
].map(devtool => ({
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
        path: path.resolve(work_path),
        publicPath: work_path,
        filename: "components.js",
        library: "components",
        libraryTarget: "umd",
    },
    plugins: [new webpack.HotModuleReplacementPlugin()],
    externals: [
        /^react.+$/
    ]
}))