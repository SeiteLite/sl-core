interface Element {
  fetchIcon(icon: str): void
}

;(() => {
  let iconCache = {}
  // fetch icon and set it as innerHTML
  let iconFromCache = (el: el, icon: str): bol => {
    let html = iconCache[icon]
    if (html) el.replaceWith(html.toEl().cla(el.cls()))
    return !!html
  }

  let p = Element.prototype
  p.fetchIcon = function (icon: str) {
    if (!icon) return
    iconFromCache(this, icon) ||
      fetch(SLG.SL + `assets/icons/${icon}.svg` + SLG.bust)
        .then((res) => res.text())
        .then((html) => {
          iconCache[icon] = html
            .replace(' class="', ' class="icon ')
            .replace('</svg>', `<title></title></svg>`)
          iconFromCache(this, icon)
        })
  }
})()
