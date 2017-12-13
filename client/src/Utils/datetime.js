export const date = (o) => {
  //([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))

  if(o.length <= 4 && !isNaN(o)) {
    return true;
  }

  if(o.match(/^\d{4}-$/)){
    return true;
  }

  if(o.match(/^\d{4}-(0[1-9]{0,1}|1[0-2]{0,1})$/)){
    return true;
  }

  if(o.match(/^\d{4}-(0[1-9]{0,1}|1[0-2]{0,1})-$/)){
    return true;
  }

  if(o.match(/^\d{4}-(0[1-9]{0,1}|1[0-2]{0,1})-(0[1-9]{0,1}|[12]\d{0,1}|3[01]{0,1})$/)){
    return true;
  }

  return false;
}

export const time = (o) => {
  if(o.length <= 2 && !isNaN(o) && o <=24) {
    return true;
  }

  if(o.match(/^(0[0-9]{0,1}|[12][0-4]{0,1})$/)){
    return true;
  }

  if(o.match(/^(0[0-9]{0,1}|[12][0-4]{0,1}):$/)){
    return true;
  }

  if(o.match(/^(0[0-9]{0,1}|[12][0-4]{0,1}):(0[0-9]{0,1}|[1-5][0-9]{0,1})$/)){
    return true;
  }

  return false;

}
