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
    placeholder: 'id'
  },
  {
    name:"pnum",
    label:"Patient Number",
    placeholder: 'Patient Number...'
  },
  {
    name:"blood_type",
    label:"Blood Type",
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
    placeholder: 'Blood Sugar...'
  },
];

export class Patient extends Component {
  constructor(props) {
    super(props);

    this.state = {
      person: {},
      patient: {}
    };

  }
  onSubmit(fields) {
    console.log('Patient#onSubmit', fields);
    this.setState({
      ...this.state,
      patient: {...fields}
    });

    // Save the person object.
    PersonService.save(this.state.person)
      .then((res) => {
        console.log('PersonService.save#res', res);

        return PatientService.save(this.state.patient)
      }).then((res) => {
        console.log('PatientService.save#res', res);

    });

    // Save the patient object.

  }

  onChange(fields) {
    console.log('Patient#onChange', fields);
    this.setState({
      ...this.state,
      patient: {...fields}
    });
  }

  onPersonSubmit(fields) {
    console.log('onPersonSubmit', fields);



  }

  onPersonChange(fields) {
    //console.log('onPersonChange', fields);
    this.setState({
      ...this.state,
      person: {...fields}
    });
  }

  render() {
    return (
      <div>
        <h2>Patient Information</h2>
        <Person
          onChange={ this.onPersonChange.bind(this) } />
        <Form title="Patient Information"
              fields={fields}
              onChange={ this.onChange.bind(this) }
              onSubmit={ this.onSubmit.bind(this) } />
        <Conditions patient={2} />
      </div>
    );
  }
}
