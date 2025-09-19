// --- responsivity ---
doc.whenDone(() => {
  // breakpoints
  let mobile = '650px'
  let tablet = '1023px'

  // queries
  let mobileMatch = win.matchMedia(`(max-width: ${mobile})`)
  let tabletMatch = win.matchMedia(`(max-width: ${tablet})`)

  // responsive class names
  let clsMobile = 'MBL'
  let clsTablet = 'TBL'

  // body class
  let set = (cls: str, val?: bol) => doc.body.tgl(cls, val)

  // initial match
  set(clsMobile, mobileMatch.matches)
  set(clsTablet, tabletMatch.matches)

  // width changes
  mobileMatch.addEventListener('change', (e) => set(clsMobile, e.matches))
  tabletMatch.addEventListener('change', (e) => set(clsTablet, e.matches))
})
