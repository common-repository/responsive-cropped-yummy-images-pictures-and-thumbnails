/* eslint-env commonjs */

const path = require('path');
const webpack = require('webpack');

const { VueLoaderPlugin } = require('vue-loader');

const dirSource = path.resolve(__dirname, 'app/js');
const dirAssets = path.resolve(__dirname, './assets');

const config = {
  mode: 'development',
  devtool: 'source-map',
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
    filename: 'bundle.js',
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
  resolve: {
    alias: {
      vue$: 'vue/dist/vue.esm.js',
    },
  },
};

// vue loader
config.module.rules.push({
  test: /\.vue$/,
  use: ['vue-loader', 'vue-template-loader'],
});

// vue
config.plugins.push(new VueLoaderPlugin());

module.exports = config;