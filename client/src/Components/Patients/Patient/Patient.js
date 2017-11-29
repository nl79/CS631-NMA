import React, { Component } from 'react';
import { Person } from '../../Person';
import { Form } from '../../Common';

import { PatientService } from '../../../Services/HttpServices/PatientService';

const fields = [
  //{ id:"name", label:"Client Name" },
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"pnum",
    label:"Patient Number",
    placeholder: 'Patient Number...',
    disabled: true
  },
  {
    name:"blood_type",
    label:"Blood Type",
    value:"",
    type:"select",
    options:['o+', 'o-', 'a+', 'a-', 'b+', 'b-', 'ab+', 'ab-'],
    default: 'o+',
    placeholder: 'Blood Type...'
  },
  {
    name:"admit_date",
    label:"Date of Admission",
    type:"date",
    placeholder: 'Date of Admission...'
  },
  {
    name:"cholesterol",
    label:"Cholesterol",
    placeholder: 'Cholesterol...',
    type: "number",
    maxlength: 3
  },
  {
    name:"blood_sugar",
    label:"Blood Sugar",
    placeholder: 'Blood Sugar...',
    type: "number",
    maxlength: 3
  },
];

export class Patient extends Component {
  constructor(props) {
    super(props);

    this.state = {};

  }


  fetch(id) {
    if(id) {
      PersonService.get(id).then((res) => {
        this.setState({...res.data});
      });
    }
  }

  componentWillMount() {

    this.fetch(this.props.id);

  }

  componentWillReceiveProps(props) {

    // if person data has not been loaded, or does not exist. fetch it.
    if(props.id !== this.state.id) {
      this.fetch(props.id);
    }

  }

  onSubmit(fields) {
    // Save the person object.
    PatientService.save(fields)
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

  render() {
    return (
      <Form
        title="Patient Information"
        fields={fields}
        data={this.state}
        onChange={ this.onChange.bind(this) }
        onSubmit={ this.onSubmit.bind(this) } />
    );
  }
}
