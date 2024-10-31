/* eslint-env commonjs */

const path = require('path');
const webpack = require('webpack');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

const dirSource = path.resolve(__dirname, 'app/js');
const dirAssets = path.resolve(__dirname, './assets');

module.exports = {
  mode: 'production',
  devtool: 'none',
  context: dirSource,
  externals: {
    jquery: 'jQuery',
  },
  entry: {
    app: [
      path.resolve(dirSource, './index.js'),
    ],
  },
  output: {
    path: path.resolve(dirAssets, './js'),
    filename: 'bundle.min.js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        enforce: 'pre',
        exclude: /(node_modules|assets|tests|\.spec\.js)/,
        use: [
          {
            loader: 'eslint-loader',
            options: {
              failOnWarning: false,
              failOnError: false,
            },
          },
        ],
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            cacheDirectory: true,
            presets: [
              ['env', {
                targets: {
                  browsers: [
                    'last 5 versions',
                    'safari >= 8',
                    'ie >= 10',
                  ],
                },
              }],
            ],
          },
        },
      },
      {
        test: /\.pug/,
        loaders: ['html-loader', 'pug-html-loader'],
      },
    ],
  },
  plugins: [
    new webpack.LoaderOptionsPlugin({
      options: {
        eslint:
        {
          failOnWarning: false,
          failOnError: false,
          fix: true,
          quiet: false,
        },
      },
    }),
    new webpack.NoEmitOnErrorsPlugin(),
  ],
  optimization: {
    minimizer: [
      new UglifyJsPlugin({
        cache: '.js-cache',
        parallel: true,
        sourceMap: false,
        uglifyOptions: {
          ecma: 5,
          compress: true,
          output: {
            comments: false,
            beautify: false,
          },
        },
      }),
    ],
  },
  resolve: {
    alias: {},
  },
};
