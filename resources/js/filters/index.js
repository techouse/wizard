import Vue        from "vue"
import { format } from "date-fns"

Vue.filter("formatDate", (value, formatString = "YYYY-MM-DD HH:mm:ss") => format(new Date(value), formatString))

Vue.filter("localeDateString", (value) => new Date(value)
    .toLocaleDateString(navigator.language, {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    })
    .replace(/,/g, ""))

Vue.filter("truncate", (text = "", length = 30, clamp = "...") => {
    if (text.length <= length) {
        return text
    }

    let tcText = text.slice(0, length - clamp.length)
    let last = tcText.length - 1

    while (last > 0 && tcText[last] !== " " && tcText[last] !== clamp[0]) {
        --last
    }

    last = last || length - clamp.length
    tcText = tcText.slice(0, last)

    return tcText + clamp
})

Vue.filter("truncateMiddle", (text, length = 30, clamp = "...") => {
    if (text.length <= length) {
        return text
    }

    const sepLen = clamp.length
    const charsToShow = length - sepLen
    const frontChars = Math.ceil(charsToShow / 2)
    const backChars = Math.floor(charsToShow / 2)

    return text.substr(0, frontChars) + clamp + text.substr(text.length - backChars)
})
