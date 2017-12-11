import React, { Component } from 'react';
import { Form } from '../Common';
import { State } from '../../Utils';

import { PersonService } from '../../Services/HttpServices/PersonServices';

const fields = [
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
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
    placeholder: 'SSN..',
    type: "number",
    maxlength: 9

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
    placeholder: 'Phone Number..',
    type: "number",
    maxlength: 10
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

    this.state = {
      data: '',
      error: ''
    };

  }

  fetchPerson(id) {
    if(id) {
      PersonService.get(id).then((res) => {
        this.setState({data: {...res.data}}, (o) => {
          if(this.props.onLoad) {
            this.props.onLoad(this.state.data);
          }
        });
      });
    }
  }

  componentWillMount() {
    this.fetchPerson(this.props.id);
  }

  componentWillReceiveProps(props) {

    if(!props.id){
      this.setState((e) => {
        return {data: {...State.reset(e.data)}}
      });
    }
    // if person data has not been loaded, or does not exist. fetch it.
    if(props.id !== this.state.data.id) {
      this.fetchPerson(props.id);
    }

  }

  onSubmit(fields) {
    // Save the person object.
    PersonService.save(fields)
      .then((res) => {

        if(res.data.id) {
          this.setState({data: {...res.data}});
          if(this.props.onSubmit) {
            this.props.onSubmit(res.data);
          }
        } else {
          // Report Error
        }

      });
  }

  onChange(fields) {
    this.setState({data: {...fields}});
    if(this.props.onChange) {
      this.props.onChange(fields);
    }
  }

  onReset() {

  }

  render() {
    return (
      <Form
        title="Personal Information"
        fields={fields}
        data={this.state.data}
        onSubmit={ this.onSubmit.bind(this) }/>
    );
  }
}
