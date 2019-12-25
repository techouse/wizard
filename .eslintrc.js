module.exports = {
    parserOptions: {
        "ecmaVersion": 2018,
        "parser": "babel-eslint",
        "sourceType": "module"
    },
    extends: [
        "airbnb-base",
        "eslint:recommended",
        "plugin:import/recommended",
        "plugin:import/errors",
        "plugin:import/warnings",
        "plugin:vue/recommended",
        "plugin:vue-i18n/recommended",
    ],
    globals: {
        "process": true,
        "require": true
    },
    env: {
        browser: true,
        es6: true,
        node: true
    },
    settings: {
        "import/resolver": {
            node: {
                extensions: [".mjs", ".js", ".json", ".vue"]
            },
            alias: {
                map: [
                    ["~", "./resources/sass/"],
                    ["@", "./resources/js/"]
                ],
                extensions: [".ts", ".js", ".jsx", ".json", ".vue"]
            },
        },
        "import/extensions": [
            ".js",
            ".mjs",
            ".jsx",
            ".vue"
        ],
        "import/core-modules": [],
        "import/ignore": [
            "node_modules",
            "\\.(coffee|scss|css|less|hbs|svg|json)$",
        ],
        "vue-i18n": {
            localeDir: "./resources/js/i18n/*.json"
        }
    },
    rules: {
        "no-multi-spaces": 0,
        "no-debugger": process.env.NODE_ENV === "production" ? 2 : 0,
        "no-console": 0,
        "camelcase": "off",
        "indent": "off",
        "linebreak-style": ["error", "unix"],
        "quotes": ["error", "double"],
        "semi": ["error", "never"],
        "max-len": ["error", 120, 2, {
            ignoreUrls: true,
            ignoreComments: false,
            ignoreRegExpLiterals: true,
            ignoreStrings: true,
            ignoreTemplateLiterals: true,
        }],
        "no-param-reassign": ["error", { "props": false }],
        "no-shadow": ["error", { "allow": ["state"] }],
        "object-curly-newline": "off",
        "vue/html-indent": ["error", 4],
        "vue/max-attributes-per-line": 0,
        "import/extensions": ["error", "always", {
            js: "never",
            mjs: "never",
            jsx: "never",
            ts: "never",
            tsx: "never",
            vue: "never"
        }],
        "no-new": "off",
        "no-underscore-dangle": ["error", {
            "allow": [
                "_remove",
                "_bulkRemove",
                "_getData",
                "_beforeRouteUpdate",
                "_getModel",
                "_submit",
                "_remove",
                "_multiplier"
            ]
        }],
        "vue-i18n/no-dynamic-keys": "error",
        "vue-i18n/no-unused-keys": ["error", {
            extensions: [".js", ".vue"]
        }]
    },
    plugins: [
        "import",
        "vue"
    ]
}
