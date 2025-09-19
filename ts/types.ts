// --- shortcuts ---
type str = string
type bol = boolean
type num = number
type int = number & {__int__: void}

// --- callbacks ---
type cb = () => void
type cbNum = (arg: num) => void

// --- types ---

type el = HTMLElement
type tag = str
type elTag = el | tag
