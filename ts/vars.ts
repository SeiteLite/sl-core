// --- shortcuts ---
var doc = document
var win = window

// --- SeiteLite globals ---
var SLG: {
  SL: ''
  isDev: 0
  bust: ''
  reVars: {[key: str]: ReVar<any>}
  go: (loc: str, newTab: bol) => void
}
