import * as React from 'react';

export const Select = (props) => {
  const { config, value } = props;
  return (
    <select
      defaultValue={value}
      className="form-control"
      onChange={
        (e) => { props.onChange(e.target.value) }
      }>
      {
        config.options.map((o, i) => {

          return (typeof o === 'string' )
          ? <option key={i} value={o}>{o}</option>
          : <option key={i} value={o.key || ''}>{o.value || ''}</option>

        })
      }
    </select>
  );
};
