// webpack.settings.js - webpack settings config

// node modules
require('dotenv').config();

// Webpack settings exports
// noinspection WebpackConfigHighlighting
module.exports = {
    name: "PrintPlugin",
    copyright: "WideWeb",
    node: {
        fs: 'empty',
        net: 'empty'
    },
    target: 'node',
    paths: {
        src: {
            base: "./src/assetbundles/printplugin/src/",
            css: "./src/assetbundles/printplugin/src/css/",
            js: "./src/assetbundles/printplugin/src/js/"
        },
        dist: {
            base: "./src/assetbundles/printplugin/dist/",
            clean: [
                '**/*'
            ]
        },
        templates: "./src/templates/"
    },
    urls: {
        publicPath: () => process.env.PUBLIC_PATH || "",
    },
    vars: {
        cssName: "styles"
    },
    entries: {
        "PrintPlugin": "index.js",
    },
    babelLoaderConfig: {
        exclude: [
            /(node_modules|bower_components)/
        ],
    },
    copyWebpackConfig: [
    ],
    devServerConfig: {
        public: () => process.env.DEVSERVER_PUBLIC || "http://localhost:8080",
        host: () => process.env.DEVSERVER_HOST || "localhost",
        poll: () => process.env.DEVSERVER_POLL || false,
        port: () => process.env.DEVSERVER_PORT || 8080,
        https: () => process.env.DEVSERVER_HTTPS || false,
    },
    manifestConfig: {
        basePath: ""
    },
    purgeCssConfig: {
        paths: [
            "./src/templates/**/*.{twig,html}",
        ],
        whitelist: [
            "./src/assetbundles/printplugin/src/css/components/*.css",
            // "./node_modules/tokenfield/dist/tokenfield.css",
            // "./node_modules/@riophae/vue-treeselect/dist/vue-treeselect.css"
        ],
        whitelistPatterns: [],
        extensions: [
            "html",
            "js",
            "twig"
        ]
    },
    saveRemoteFileConfig: [
    ],
    createSymlinkConfig: [
    ],
};
