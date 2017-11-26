import * as React from 'react';

export const Radio = (props) => {
  const { config } = props;
  return (
    <div>
      {
        config.options.map((o, i) => {
          return ('');
        })
      }
    </div>
  );
};
