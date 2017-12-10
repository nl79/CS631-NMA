import React, { Component } from 'react';

import { Appointment } from "./Appointment";
import { Profile } from '../../Patients/Patient/Profile';
export class View extends Component {
  constructor(props) {
    super(props);

    this.state = {
      id: '',
      patient: ''
    };

  }

  componentWillReceiveProps(props) {
    let id = props.id || props.routeParams.id;
    if(!id) {
      this.setState({
        id: '',
        patient: ''
      });
    }
  }

  componentWillMount() {
    // Check if an id was supplied
    let id = this.props.id || this.props.routeParams && this.props.routeParams.id || '',
        patient = this.props.routeParams && this.props.routeParams.patient || '';

    if(id || patient) {
      this.setState({
        id: id,
        patient: patient
      });
    }
  }

  onAppointmentSubmit(fields) {

    this.setState(
      {
        ...this.state,
        id: fields.id
      }
    );
  }

  onAppointmentLoad(data) {
    if(data.patient) {
      this.setState({
        patient: data.patient
      });
    }
  }

  render() {
    return (
      <div>
        <h2>Appointment Information</h2>
        <Profile id={ this.state.patient }/>
        <Appointment
          id={this.state.id}
          patient={ this.state.patient || ''}
          onSubmit={ this.onAppointmentSubmit.bind(this) }
          onLoad={this.onAppointmentLoad.bind(this) } />
      </div>
    )
  }

}
