const path = require("path")
const webpack = require("webpack")

module.exports = {
    resolve: {
        alias: {
            "~": path.resolve(__dirname, "resources/sass/"),
            "@": path.resolve(__dirname, "resources/js/"),
        }
    },
}
