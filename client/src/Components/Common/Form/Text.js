import * as React from 'react';

export const Text = (props) =>{

  let onChange = (e) => {
    let value = e.target.value;



    if(props.config) {
      let c = props.config;

      if(c.format) {
        value = c.format(value);
      }

      if(c.validate && !c.validate(value)) {
        value = props.value;
      }

      // Check Max Value
      if(c.maxlength && c.maxlength < value.length) {
        value = props.value;
      }

    }
    props.onChange(value);
  };

  return (
    <input type={ props.config.type || "text" }
            className={ props.className }
            name={props.name || '' }
            value={props.value}
            onChange={onChange}
            onKeyPress={props.config.onKeyPress}
            id={'id'}
            disabled={props.config.disabled || '' }
            maxLength={props.config.maxlength || ''}
            placeholder={props.config.placeholder || '' } />
  );
};
