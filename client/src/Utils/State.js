export const reset = (o) => {
  let state = {},
      keys = Object.keys(o);

  for( let key in o) {
    if(o.hasOwnProperty(key)) {
      o[key] = '';
    }
  }

  return o;
}
