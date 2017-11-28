import React, { Component } from 'react';
import { Form } from '../Common';

import { PersonService } from '../../Services/HttpServices/PersonServices';


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
    type: "date",
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
    value:"n/a",
    type:"select",
    options:['n/a', 'm', 'f'],
    default: 'n/a'
  }
];

export class Person extends Component {
  constructor(props) {
    super(props);

    this.state = {};

  }

  fetchPerson(id) {
    console.log('fetchPerson', id);
    if(id) {
      PersonService.get(id).then((res) => {
        this.setState({...res.data});
      })
    }
  }

  componentWillMount() {

    this.fetchPerson(this.props.id);

  }

  componentWillReceiveProps(props) {
    //console.log('Person#componentWillReceiveProps#props', props);

    // if person data has not been loaded, or does not exist. fetch it.
    if(props.id !== this.state.id) {
      this.fetchPerson(props.id);
    }

  }

  onSubmit(fields) {
    // Save the person object.
    PersonService.save(fields)
      .then((res) => {
        this.setState({...res.data});
        if(this.props.onSubmit) {
          this.props.onSubmit(res.data);
        }
      });
  }

  onChange(fields) {
    this.setState({...fields});
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
            data={this.state}
            onChange={ this.onChange.bind(this) }
            onSubmit={ this.onSubmit.bind(this) }/>
    );
  }
}
