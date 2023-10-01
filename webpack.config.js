/**
 * External dependencies
 */
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RtlCssPlugin = require('rtlcss-webpack-plugin');

const { compact } = require('lodash');
const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    resolve: {
        ...defaultConfig.resolve,
        alias: {
            "react/jsx-runtime.js": "react/jsx-runtime",
            "react/jsx-dev-runtime.js": "react/jsx-dev-runtime",
            '@quillsmtp/navigation': path.resolve(__dirname, 'src/navigation'),
            "@quillsmtp/connections": path.resolve(__dirname, 'src/connections'),
            "@quillsmtp/config": path.resolve(__dirname, 'src/config'),
            "@quillsmtp/mailers": path.resolve(__dirname, 'src/mailers'),
        },
    },
    module: {
        ...defaultConfig.module,
        rules: compact([
            {
                test: /\.jsx?$/,
                use: [
                    {
                        loader: 'babel-loader?cacheDirectory',
                        options: {
                            presets: [
                                [
                                    '@babel/preset-env',
                                    {
                                        modules: false,
                                        targets: {
                                            browsers: [
                                                'extends @wordpress/browserslist-config',
                                            ],
                                        },
                                    },
                                ],
                            ],
                            plugins: [
                                require.resolve(
                                    '@babel/plugin-proposal-object-rest-spread'
                                ),
                                require.resolve(
                                    '@babel/plugin-transform-react-jsx'
                                ),
                                require.resolve(
                                    '@babel/plugin-proposal-async-generator-functions'
                                ),
                                require.resolve(
                                    '@babel/plugin-transform-runtime'
                                ),
                                require.resolve(
                                    '@babel/plugin-proposal-class-properties'
                                ),
                            ].filter(Boolean),
                        },
                    },
                ],
                include: [
                    path.resolve(__dirname, 'client'),
                ],
                exclude: /node_modules/,
            },
            {
                test: /\.s?css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        // postcss loader so we can use autoprefixer and theme Gutenberg components
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: {
                                config: path.resolve(
                                    __dirname,
                                    'postcss.config.js'
                                ),
                            },
                        },
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            additionalData:
                                '@import "src/base-styles/_colors"; ' +
                                '@import "src/base-styles/_variables"; ' +
                                '@import "src/base-styles/_breakpoints"; ' +
                                '@import "src/base-styles/_mixins"; ',
                        },
                    }
                ],
            },
            {
                test: /\.tsx?$/,
                use: [
                    {
                        loader: 'ts-loader',
                        options: {
                            transpileOnly: true,
                        },
                    },
                ],
            },
        ]),
    },
    plugins: [
        // Remove css file from default config
        ...defaultConfig.plugins.filter(
            (plugin) => !(plugin instanceof MiniCssExtractPlugin)
        ),
        new MiniCssExtractPlugin({
            filename: 'style.css',
        }),
        new RtlCssPlugin("style-rtl.css"),
    ],
    output: {
        ...defaultConfig.output,
        filename: 'index.js',
    },
};
