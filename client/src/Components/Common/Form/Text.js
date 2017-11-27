import * as React from 'react';

export const Text = (props) =>{
  return (
    <input type={ props.config.type || "text" }
            className={ props.className }
            name={props.name || '' }
            value={props.value}
            onChange={(e)=>{ props.onChange(e.target.value) }}
            id={'id'}
            disabled={props.config.disabled || '' }
            maxLength={props.config.maxlength || ''}
            placeholder={props.config.placeholder || '' } />
  );
};
