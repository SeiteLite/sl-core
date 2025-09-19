doc.whenDone(() => {
  let aside = doc.qSel('body > aside')
  let menu = doc.qSel('body > main #menu')
  let back = doc.qSel('body > aside #menu')

  SLG.nav.aside = aside

  menu.clk(() => aside.tgl('active'))
  back.clk(() => aside.tgl('active'))
})

let fetchPage = (path, replace) => {
  let href = SLG.nav.pagesUrl + path
  if (replace) history.replaceState(path, '', href)
  else history.pushState(path, '', href)

  navPage(path)

  let url = new URL(SLG.nav.fetchPhp)
  url.searchParams.set('path', SLG.nav.basePath + path)

  fetch(url)
    .then((res) => res.text())
    .then((html) => {
      doc.qSel('body > main > article').innerHTML = html
    })
}

let gotoPage = (path) => {
  SLG.nav.aside.tgl('active', false)
  fetchPage(path, false)
}

let navPage = (path) => {
  let lis = doc.qAll('body > aside > nav li')
  lis.forEach((li) => {
    let attr = li.getAttribute('path')
    li.tgl('active', path.startsWith(attr))
    li.tgl('selected', path == attr)
  })
}

let onLis = () => {
  let lis = doc.qAll('body > aside > nav li')
  let go = (li) => gotoPage(li.getAttribute('path'))
  lis.forEach((li) => (li.onclick = () => go(li)))
}

// scroll to footnote
let scrFn = (id) => {
  let anchor = doc.qSel('body > main > article #' + id)
  anchor?.scrollIntoView({behavior: 'smooth'})
}

win.addEventListener('popstate', (event) => {
  let path = event.state || ''
  fetchPage(path, true)
})
