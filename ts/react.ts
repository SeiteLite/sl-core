// reactivity
type ReFn<T> = (val: T) => void

interface _Elem {
  revar(): void
}

class ReVar<T> {
  _val: T

  constructor(val: T) {
    this._val = val
  }

  get val(): T {
    return this._val
  }

  set val(val: T) {
    if (this._val != val) this._val = val
    this.notify()
  }

  _subd = new Set<ReFn<T> | _Elem>()

  sub(f: ReFn<T> | _Elem) {
    this._subd.add(f)
  }

  unsub(f: ReFn<T> | _Elem) {
    this._subd.delete(f)
  }

  notify() {
    this._subd.forEach((f) =>
      f.isFun() ? (f as ReFn<T>)(this._val) : (f as _Elem).revar(),
    )
  }
}
