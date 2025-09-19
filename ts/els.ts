SLG.reVars = {}

// base classes for custom html elements
class _Elem extends HTMLElement {
  root: ShadowRoot | HTMLElement
  rv: ReVar<any> // react

  constructor() {
    super()
    // reactive variable
    this.rv = SLG.reVars[this.attr('$')] || new ReVar(0)
    // common behaviour
    let attr: string
    if ((attr = this.attr('go'))) {
      this.tgl('ptr', true)
      this.onclick = (e) => SLG.go(attr, e.ctrlKey || this.hasAttr('blank'))
    } else if ((attr = this.attr('on'))) {
      this.tgl('ptr', true)
      this.onclick = () => new Function(attr).call(this)
    }
  }

  _tplCl() {
    // clone template if availablem (it is optional for light DOM elements)
    let tplId = (this.constructor as CustomElementConstructor).tag
    let tpl = (doc.qId(tplId) as HTMLTemplateElement)?.content
    tpl && this.root.appendChild(tpl.cloneNode(true))
  }

  connectedCallback() {
    this.rv.sub(this)
    this.init()
    this.revar()
  }

  disconnectedCallback() {
    this.rv.unsub(this)
    this.done()
  }

  init() {}
  done() {}

  revar() {}

  // --- attributes ---

  // test attribute existence
  hasAttr(attr: str): bol {
    return this.hasAttribute(attr)
  }

  // get attribute
  attr(attr: str, def = ''): str {
    return this.getAttribute(attr) ?? def
  }

  // first attribute w/o value
  attr0(): str {
    for (let attr of this.attributes as any as Attr[]) if (!attr.value) return attr.name
    return ''
  }

  // get as num
  numAttr(attr: str, def = 0): num {
    return this.attr(attr).toNum(def)
  }

  // get as int
  intAttr(attr: str, def = 0 as int): int {
    return this.attr(attr).toInt(def)
  }

  // get as object
  objAttr(attr: str, def = null): any {
    return this.attr(attr).toObj(def)
  }

  // set attribute
  setAttr(attr: str, val = '') {
    this.setAttribute(attr, val)
  }

  // remove attribute
  remAttr(attr: str) {
    this.removeAttribute(attr)
  }

  // toggle no-value attribute
  tglAttr(attr: str, on?: bol) {
    if (undefined === on) on = !this.hasAttr(attr)
    on ? this.setAttr(attr) : this.remAttr(attr)
  }

  // --- inner nodes ---

  // selector
  qSel(sel: str): el {
    return this.root.querySelector(sel)
  }

  // selector
  qAll(sel: str): NodeListOf<el> {
    return this.root.querySelectorAll(sel)
  }

  // selector
  qId(id: str): el {
    return this.qSel('#' + id)
  }

  // --- slot ---

  // slot nodes
  slotNodes(): Node[] {
    return (this.qSel('slot') as HTMLSlotElement).assignedNodes()
  }

  // slot nodes, filtered by tag
  slotTagNodes(tag: str): Node[] {
    let nodes = this.slotNodes()
    tag = tag.toUpperCase()
    return nodes.filter((node) => tag == node.nodeName)
  }

  // slot text
  slotText(tag = '', def = ''): str {
    return (tag ? this.slotTagNodes(tag) : this.slotNodes())[0]?.textContent || def
  }

  // slot number
  slotNum(tag = '', def = 0): num {
    return this.slotText(tag).toNum(def)
  }

  // slot integer
  slotInt(tag = '', def = 0 as int): int {
    return this.slotText(tag).toInt(def)
  }

  // slot object
  slotObj(tag = '', def = null): any {
    return this.slotText(tag).toObj(def)
  }

  // slot selector
  qSlot(sel: str): el {
    return this.querySelector(sel)
  }

  // --- handle nodes ---

  // tag or element to element
  _el(elTag: elTag): el {
    return elTag.isStr() ? this.qSel(elTag as str) : (elTag as el)
  }

  // slot tag or element to element
  _slotEl(elTag: elTag): el {
    return elTag.isStr() ? this.qSlot(elTag as str) : (elTag as el)
  }

  // append child to root
  apdRoot(elTag: elTag): el {
    return this.root.apd(elTag)
  }

  // move el under toElTag
  move(toElTag: elTag, el: el) {
    this._el(toElTag).appendChild(this._slotEl(el))
  }

  // set inner HTML
  setHtml(elTag: elTag, html: str) {
    this._el(elTag).innerHTML = html
  }

  // --- view ---

  setPos(left: int, top: int) {
    this.style.left = left + 'px'
    this.style.top = top + 'px'
  }

  // callback when host width changes
  onWidth(cb: cbNum) {
    new ResizeObserver((els) => {
      let {clientWidth} = els[0].target
      cb(clientWidth)
    }).observe(this)
  }

  // callback when host comes into view (once)
  onView(cb: cb) {
    new IntersectionObserver((els, observer) => {
      if (0 < els[0].intersectionRatio) {
        observer.disconnect() // call only once
        cb()
      }
    }).observe(this)
  }

  // --- reactive attributes ---
  static reAttrs: str[] = []

  static get observedAttributes() {
    return this.reAttrs
  }

  attributeChangedCallback(name: str, oldVal: any, newVal: any) {
    if (oldVal != newVal) this.reatr(name, newVal)
  }

  // called on reactive attribute change
  reatr(name: str, val: any) {}
}

// --- define custom elements ---
interface CustomElementConstructor {
  tag: str
}

// common shadow DOM style
let shadowStyle = new CSSStyleSheet()

// make css rules available in shadow DOM
for (let sheet of doc.styleSheets as any as CSSStyleSheet[]) {
  // based on title: transfer rules
  if ('shadow' == sheet.title)
    for (let rule of sheet.cssRules as any as CSSRule[])
      shadowStyle.insertRule(rule.cssText)
}

// shadow DOM element
class ShadowElem extends _Elem {
  constructor() {
    super()
    this.root = this.attachShadow({mode: 'open'})
    this._tplCl()
    // style
    this.root.adoptedStyleSheets = [shadowStyle]
  }
}

// light DOM element
class LightElem extends _Elem {
  constructor() {
    super()
    this.root = this
    this._tplCl()
  }
}

// define element
let defElem = (tag: str, cls: CustomElementConstructor) => {
  cls.tag = tag
  customElements.define(tag, cls)
}
