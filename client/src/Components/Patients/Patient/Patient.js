import React, { Component } from 'react';
import { Person } from '../../Person';
import { Form } from '../../Common';

import { Conditions } from '../Conditions';

import { PersonService } from '../../../Services/HttpServices/PersonServices';
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
    default: '',
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
    placeholder: 'Cholesterol...'
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

  componentWillMount() {
    // Check if an id was supplied
    let id = this.props.id || this.props.routeParams.id;

    if(id) {
      PatientService.get(id).then((res) => {
        this.setState({
          ...this.state
        });
      })
    }
  }

  componentDidUpdate() {
  }

  onSubmit(fields) {
    PatientService.save(fields)
      .then((res) => {
        console.log('PatientService.save#res', res);
        this.setState({
          ...res.data
        });
      });
  }

  onChange(fields) {

    this.setState({
      ...fields,
      ...this.state
    });
  }

  onPersonSubmit(fields) {

    this.setState(
      {
        ...this.state,
        id: fields.id
      }
    );
  }

  onPersonChange(fields) {
  }


  renderPatientData(id) {
    if(!id) { return null }

    return (
      <div>
        <Form title="Patient Information"
              fields={fields}
              data={this.state}
              onChange={ this.onChange.bind(this) }
              onSubmit={ this.onSubmit.bind(this) } />
        <Conditions patient={2} />
      </div>
    )
  }

  render() {
    console.log('this.state', this.state);
    return (
      <div>
        <h2>Patient Information</h2>
        <Person
          id={this.state.id}
          onSubmit={ this.onPersonSubmit.bind(this) } />

          { this.renderPatientData(this.state.id) }

      </div>
    )
  }

}
