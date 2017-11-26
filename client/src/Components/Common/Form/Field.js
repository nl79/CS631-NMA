import * as React from 'react';
import {
  Text,
  Radio,
  Select
} from './index'

export const Field = (props) =>{
  switch(props.config.type) {
    case 'radio':
    return <Radio {...props} />
    case 'select':
    return <Select {...props} />
    default:
    return <Text {...props} />
  }
};
