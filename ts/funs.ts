// change location
SLG.go = (loc: str, newTab = false) => {
  if (newTab) win.open(loc, '_blank')
  else {
    let [path, id] = loc.split('#')
    if (location.pathname == path) {
      // already on required page, scroll
      let q = doc.qId(id)
      q
        ? q.scrollIntoView({
            behavior: 'smooth',
          })
        : win.scrollTo({
            top: 0,
            behavior: 'smooth',
          })
    } else {
      location.assign(loc)
    }
  }
}
