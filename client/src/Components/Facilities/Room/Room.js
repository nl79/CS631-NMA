import React, { Component } from 'react';
import { Form } from '../../Common';

const fields = [
  //{ id:"name", label:"Client Name" },
  {
    name:"name",
    label:"Name",
    placeholder: 'Name..'
  },
  {
    name:"code",
    label:"Product Code",
    placeholder: 'Product Code...'
  },
  {
    name:"status",
    label:"Status",
    value:"active",
    type:"select",
    options:['active', 'terminated'],
    default: 'terminated'
  }
];

export class Room extends Component {
  constructor(props) {
    super(props);

  }

  onSubmit(fields) {
    if(this.props.onSubmit) {
      this.props.onSubmit(fields);
    }
  }

  onChange(fields) {
    console.log('onChange#fields', fields);
  }

  onReset() {

  }

  render() {
    return (
      <Form title="Room Information"
            fields={fields}
            onChange={ this.props.onChange }
            onSubmit={ this.onSubmit.bind(this) }/>
    );
  }
}
