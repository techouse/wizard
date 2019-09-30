const path = require("path")
const dotenv = require("dotenv")
const writeJsonFile = require("write-json-file")

const env = dotenv.config({
    path: path.join(__dirname, ".env"),
})

writeJsonFile(
    path.join(__dirname, "resources/js/api_client_config.json"),
    {
        client_id: env.parsed.API_CLIENT_ID,
        client_secret: env.parsed.API_CLIENT_SECRET,
    },
)
    .then(() => {
        console.log(`Wrote API client config to ${path.join(__dirname, "resources/js/api_client_config.json")}`)
    })
