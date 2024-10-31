/* eslint-env commonjs */

const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const dirSource = path.resolve(__dirname, 'app/scss');
const dirAssets = path.resolve(__dirname, './assets');

module.exports = {
  mode: 'production',
  devtool: 'none',
  context: dirSource,
  entry: {
    app: [
      path.resolve(dirSource, './bundle.scss'),
    ],
  },
  output: {
    path: path.resolve(dirAssets, './css'),
    filename: 'bundle.css',
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '../',
            },
          },
          {
            loader: 'css-loader',
            options: {
              sourceMap: false,
              importLoaders: 1,
              url: false,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              plugins: function () {
                return [
                  require('precss'),
                  require('autoprefixer'),
                ];
              },
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: false,
            },
          },
        ],
      },
    ],
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'bundle.min.css',
    }),
  ],
};
