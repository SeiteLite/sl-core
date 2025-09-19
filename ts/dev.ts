var cl = (...args: any[]) => {
  if (SLG.isDev) console.log(...args)
  return args[0]
}
