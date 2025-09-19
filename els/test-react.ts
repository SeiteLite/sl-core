// @ts-ignore first line
class _ extends ShadowElem {
  static reAttrs = ['clr']
  span: HTMLElement

  init() {
    this.span = this.qSel('span')
    this.qSel('button').onclick = () => this.inc()
  }

  inc() {
    this.rv.val = this.rv.val + 1
  }

  revar() {
    this.span.textContent = this.rv.val
  }

  reatr(name: str, val) {
    this.intAttr(name, val)
    switch (name) {
      case 'clr':
        this.style.background = val
        break
    }
  }
}
