const path = require("path")
const dotenv = require("dotenv")
const writeJsonFile = require("write-json-file")

const env = dotenv.config({
    path: path.join(__dirname, ".env"),
})

writeJsonFile(
    path.join(__dirname, "resources/js/i18n_config.json"),
    {
        locale: env.parsed.VUE_APP_I18N_LOCALE,
        fallbackLocale: env.parsed.VUE_APP_I18N_FALLBACK_LOCALE,
    },
)
    .then(() => {
        console.log(`Wrote i18n locale config to ${path.join(__dirname, "resources/js/i18n_config.json")}`)
    })
