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
          return (
            <option
              key={i}
              value={o}>{o}</option>
          )
        })
      }
    </select>
  );
};
