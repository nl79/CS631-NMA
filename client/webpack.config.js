var webpack = require('webpack');
var path = require('path');
var HtmlWebpackPlugin = require('html-webpack-plugin');
const VENDOR_LIBS = [
  'react',
  'react-dom',
  'react-router',
  'redux',
  'redux-thunk'
];
module.exports = {
  entry: {
    // Split the code into separate chunks.
    // The main bundle and  vendor code will be written out to separates files.
    // The vendor dependencies will change alot less frequently, so they will not need
    // to be downloaded as often.

    // Main Application code.
    bundle: './src/index.js',

    // Vendor bundle files.
    // Array of strings that are names of libraries that are to be included in the bundle.
    vendor: VENDOR_LIBS
  },
  output: {
    path: path.join(__dirname, 'dist'),

    // the output filename will be the key of each property in the entry object.
    // in this case 'bundle', and 'vendor'
    filename: '[name].js'
  },
  module: {
    rules: [
      {
        use: 'babel-loader',
        test: /\.js$/,
        exclude: /node_modules/
      },
      {
        use: ['style-loader', 'css-loader'],
        test: /\.css$/
      }
    ]
  },
  plugins: [
    // the plugin will not include any vendor modules in the bundle even tho
    // the files import them. It will instead separate them and pull them in
    // from the processed vendor bundle. Common 'Chunks' between modules.
    new webpack.optimize.CommonsChunkPlugin(
      {
        names: ['vendor', 'manifest']
      }
    ),
    /*
    new HtmlWebpackPlugin({
      template: 'index.html'
    }),
    */
    // Sets properties on the browsers window object.
    // React behaves differently durring development and production.
    new webpack.DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV)
    })
  ]
};
