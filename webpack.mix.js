const mix = require("laravel-mix")
const tailwindcss = require("tailwindcss")
const postCssDiscardComments = require("postcss-discard-comments")
const { CleanWebpackPlugin } = require("clean-webpack-plugin")
const TargetsPlugin = require("targets-webpack-plugin")
const whitelister = require("purgecss-whitelister")
const webpack = require("webpack")
const env = process.env.NODE_ENV
const npm_config_argv = JSON.parse(process.env.npm_config_argv)
const isWatch = npm_config_argv.remain.some(el => el.startsWith("--watch"))

require("laravel-mix-purgecss")

let config = {}
try {
    config = require("./mixconfig")
} catch (e) {
    config = require("./mixconfig.example")
}

mix.js("resources/js/index.js", "public/js")
   .sass("resources/sass/index.scss", "public/css")
   .copy("resources/images", "public/images")
   .copy("node_modules/@fortawesome/fontawesome-free/webfonts", "public/fonts")
   .copy("node_modules/element-ui/lib/theme-chalk/fonts", "public/fonts")

   .webpackConfig({
       output: {
           // https://github.com/JeffreyWay/laravel-mix/issues/1889
           chunkFilename: "js/[name].[chunkhash].js",
       },
       plugins: [
           new CleanWebpackPlugin({
               cleanOnceBeforeBuildPatterns: [path.resolve(__dirname, "public/js"),
                                              path.resolve(__dirname, "public/css")],
               cleanStaleWebpackAssets: !isWatch
           }),
           new webpack.NormalModuleReplacementPlugin(
               /element-ui[\/\\]lib[\/\\]locale[\/\\]lang[\/\\]zh-CN/,
               "element-ui/lib/locale/lang/en" // change language to requested one
           ),
           ...(env === "production" ? [new TargetsPlugin({
               browsers: ["last 2 versions", "ie >= 11"],
           })] : [])
       ],
   })
   .options({
       publicPath: "public",

       cleanCss: {
           level: {
               1: {
                   specialComments: "none"
               }
           }
       },

       processCssUrls: false,

       postCss: [
           postCssDiscardComments({ removeAll: true }),

           tailwindcss("./tailwind.config.js")
       ],
   })
   .purgeCss({
       enabled: env === "production",

       globs: [
           //
       ],

       whitelist: [
           ...whitelister([
               "node_modules/element-ui/lib/theme-chalk/index.css"
           ])
       ],

       whitelistPatterns: [
           //
       ],
   })
   .version()
   .sourceMaps()
   .disableSuccessNotifications()
   .browserSync({
       notify: false,
       files: [
           "public/**/*.css",
           "public/**/*.js",
           "resources/views/**/*.blade.php",
       ],
       ...config.browserSync
   })
