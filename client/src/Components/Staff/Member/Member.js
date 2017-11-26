import React, { Component } from 'react';
import { Person } from '../../Person';

import { PersonService } from '../../../Services/HttpServices/PersonServices';

import { Form } from '../../Common';

const fields = [
  //{ id:"name", label:"Client Name" },
  {
    name:"firstName",
    label:"First Name",
    placeholder: 'First Name..'
  },
  {
    name:"type",
    label:"Staff Type",
    value:"nurse",
    type:"select",
    options:['physician', 'nurse', 'surgeon'],
    default: 'nurse'
  }
];


export class Member extends Component {
  constructor(props) {
    super(props);

  }
  onSubmit(fields) {
    console.log('Member#onSubmit', fields);
  }

  onPersonSubmit(fields) {
    console.log('onPersonSubmit', fields);

    PersonService.save(fields);

  }

  onPersonChange(fields) {
    //console.log('onPersonChange', fields);

  }

  render() {
    return (
      <div>
        <h2>Staff Information</h2>
        <Person
          onChange={ this.onPersonChange.bind(this) }
          onSubmit={this.onPersonSubmit.bind(this)} />

        <Form 
              fields={fields}
              onSubmit={ this.onSubmit.bind(this) }/>
      </div>
    );
  }
}
