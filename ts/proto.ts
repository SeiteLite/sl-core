interface Object {
  isStr(): bol
  isFun(): bol
  asArr<T = any>(): Array<T>
  toArr<T = any>(): Array<T>
  toArr<T = any>(): Array<T>
}

;(() => {
  let p = Object.prototype
  p.isStr = function () {
    return this instanceof String
  }
  p.isFun = function () {
    return this instanceof Function
  }
  p.asArr = function () {
    return Array.isArray(this) ? this : [this]
  }
  p.toArr = function () {
    return Array.from(this)
  }
})()

interface Number {
  isInt(): bol
  clamp(min: num, max: num): num
  genArr(f: (i: int, n: int) => any): Array<any>
  loop(f: (i: int, n: int) => void): void
}

;(() => {
  let p = Number.prototype
  p.isInt = function () {
    return Number.isInteger(this.valueOf())
  }
  p.clamp = function (min, max) {
    return Math.max(min, Math.min(this, max))
  }
  p.genArr = function (f) {
    return Array.from({length: this}, (_, i) => f(i as int, this))
  }
  p.loop = function (f) {
    for (let i = 0; i < this; i++) f(i as int, this)
  }
})()

interface String {
  toNum: (def?: num) => num
  toInt: (def?: int) => int
  toObj: (def?: Object) => Object
  toEl: () => el
}

;(() => {
  let p = String.prototype
  p.toNum = function (def = 0) {
    let int = parseFloat(this)
    return isNaN(int) ? def : int
  }
  p.toInt = function (def = 0 as int) {
    let int = parseInt(this)
    return (isNaN(int) ? def : int) as int
  }
  p.toObj = function (def?) {
    try {
      return JSON.parse(this)
    } catch (err) {
      return def
    }
  }
  p.toEl = function () {
    let div = doc.createElement('div')
    div.innerHTML = this
    return div.firstChild as el
  }
})()

interface Set<T> {
  dif(that: Set<T>): Set<T> // difference
  uni(that: Set<T>): Set<T> // union
}

;(() => {
  let p = Set.prototype
  p.dif = function (that) {
    return new Set([...this].filter((x) => !that.has(x)))
  }
  p.uni = function (that) {
    return new Set([...this, ...that])
  }
})()

interface Node {
  // append
  apd(elTag: elTag): el
}

;(() => {
  let p = Node.prototype
  p.apd = function (elTag) {
    let el = elTag.isStr() ? doc.createElement(elTag as tag) : (elTag as el)
    return this.appendChild(el)
  }
})()

interface Element {
  cls(): str[] // list of classes
  cla(cls: str | str[]): Element // add class(es)
  tgl(cls: str, on?: bol): Element // toggle class
  clk(f: () => void): Element // click event
}

;(() => {
  let p = Element.prototype
  p.cls = function () {
    return this.classList.toArr()
  }
  p.cla = function (cs) {
    for (let c of cs.asArr()) this.classList.add(c)
    return this
  }
  p.tgl = function (cls, on) {
    this.classList.toggle(cls, on)
    return this
  }
  p.clk = function (f) {
    this.addEventListener('click', f)
    return this
  }
})()

interface Document {
  // wait for DOM content to load
  whenDone(fn: (this: Document, ev: Event) => any): void
  // selectors
  qSel: (sel: str) => Element | null
  qAll: (sel: str) => NodeListOf<Element>
  qId: (id: str) => Element | null
}

;(() => {
  let p = Document.prototype
  p.whenDone = (fn) => doc.addEventListener('DOMContentLoaded', fn)
  p.qSel = (sel: str) => doc.body.querySelector(sel)
  p.qAll = (sel: str) => doc.body.querySelectorAll(sel)
  p.qId = (id: str) => p.qSel('#' + id)
})()
