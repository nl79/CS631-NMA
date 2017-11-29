import React, { Component } from 'react';
import { Person } from '../../Person';
import { Patient } from './Patient';
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

export class View extends Component {
  constructor(props) {
    super(props);

    this.state = {};

  }

  componentWillReceiveProps(props) {
    let id = props.id || props.routeParams.id;
    if(!id) {
      this.setState({id: null}, () => {
      });
    }
  }

  componentWillMount() {
    // Check if an id was supplied
    let id = this.props.id || this.props.routeParams.id;

    if(id) {
      this.setState({id: id})
    }
  }

  onPersonSubmit(fields) {

    this.setState(
      {
        ...this.state,
        id: fields.id
      }
    );
  }

  onPatientSubmit(fields) {
    this.setState(
      {
        ...this.state,
        pnum: fields.pnum
      }
    );
  }

  onConditionSubmit(fields) {

  }

  render() {
    return (
      <div>
        <h2>Patient Information</h2>
        <Person
          id={this.state.id}
          onSubmit={ this.onPersonSubmit.bind(this) } />

        <Patient
          id={this.state.id}
          onSubmit={ this.onPatientSubmit.bind(this) }
          onLoad={ this.onPatientSubmit.bind(this) } />

        {
          this.state.pnum ?
          <Conditions
            id={this.state.id}
            onSubmit={ this.onConditionSubmit.bind(this) } /> : null
        }

      </div>
    )
  }

}
