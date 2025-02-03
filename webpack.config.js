// const path = require('path');
// const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// module.exports = {
//     entry: {
//         'frontend': ['./src/frontend/frontend.js', './src/frontend/styles/frontend.scss'],
//         'block': './src/admin/block.js'
//     },
//     output: {
//         path: path.resolve(__dirname, 'assets/js'),
//         filename: '[name].js'
//     },
//     module: {
//         rules: [
//             {
//                 test: /\.(js|jsx)$/,
//                 exclude: /node_modules/,
//                 use: {
//                     loader: 'babel-loader'
//                 }
//             },
//             {
//                 test: /\.(css|scss)$/,
//                 use: [
//                     MiniCssExtractPlugin.loader,
//                     'css-loader',
//                     'sass-loader'
//                 ]
//             }
//         ]
//     },
//     plugins: [
//         new MiniCssExtractPlugin({
//             filename: '../css/[name].css'
//         })
//     ],
//     resolve: {
//         extensions: ['.js', '.jsx', '.scss', '.css'],
//         modules: [
//             path.resolve(__dirname, 'src/frontend/styles'),
//             'node_modules'
//         ]
//     }
// };