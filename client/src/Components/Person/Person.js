import React, { Component } from 'react';
import { Form } from '../Common';

/*
	  id							int						not null auto_increment,
    ssn						int						not null,
    firstName			varchar(25)			not null,
    lastName			varchar(25)			not null,
    gender				enum('m','f')			not null,
    dob						date						not null,
    phnumb				int(10)					not null,
 */
const fields = [
  //{ id:"name", label:"Client Name" },
  {
    name:"firstName",
    label:"First Name",
    placeholder: 'First Name..'
  },
  {
    name:"lastName",
    label:"Last Name",
    placeholder: 'Last Name..'
  },
  {
    name:"ssn",
    label:"SSN",
    placeholder: 'SSN..'
  },
  {
    name:"dob",
    label:"Date of Birth",
    placeholder: 'Date of Birth(YYYY-MM-DD)..'
  },
  {
    name:"phnumb",
    label:"Phone Number",
    placeholder: 'Phone Number..'
  },
  {
    name:"gender",
    label:"Gender",
    value:"",
    type:"select",
    options:['', 'm', 'f'],
    default: ''
  }
];

export class Person extends Component {
  constructor(props) {
    super(props);

  }

  onSubmit(fields) {
    if(this.props.onSubmit) {
      this.props.onSubmit(fields);
    }
  }

  onChange(fields) {
    if(this.props.onChange) {
      this.props.onChange(fields);
    }
  }

  onReset() {

  }

  render() {
    return (
      <Form title="Personal Information"
            fields={fields}
            onChange={ this.props.onChange }
            onSubmit={ this.props.onSubmit }/>
    );
  }
}
