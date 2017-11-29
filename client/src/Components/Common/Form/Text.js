import * as React from 'react';

export const Text = (props) =>{

  let onChange = (e) => {
    let value = e.target.value;

    if(props.config) {
      let c = props.config;

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
            id={'id'}
            disabled={props.config.disabled || '' }
            maxLength={props.config.maxlength || ''}
            placeholder={props.config.placeholder || '' } />
  );
};
