export const parseError = (e) => {
  let err = e.length && e[0].length ? e[0] : null;
      err = err.length >= 3 && err[0] === 'Row' && err[1] === 'Row::validate' ? err[3] : null;

  let list = {};
  let temp;

  if(err) {
    for(var key in err) {
      if(err.hasOwnProperty(key)) {
        temp = err[key].length ? err[key][0] : null;
        temp = temp.length >= 2  ? temp[2] : null;

        list[key] = temp;
      }
    }
  } else {
    list = 'Error Occurect - Please Validate your Input'
  }
  return list;
}
