import React, { Component } from 'react';
import { browserHistory} from 'react-router';

import { List } from '../../Patients/List';
import { View } from './View';

export class Dashboard extends Component {
  constructor(props) {
    super(props);

    this.state = {
      patient: ''
    };
  }

  componentWillMount() {
  }

  componentWillReceiveProps(props) {

  }

  onPatientSelect(o) {
    browserHistory.push(`/scheduling/appointments/patients/${o.id}/new`);
    //this.setState({patient: o});
  }

  render() {
    return (
      <div>
        <h2>Appointment Management</h2>
          {
            !this.state.patient
              ? <List onSelect={this.onPatientSelect.bind(this)}/>
              : <View patient={this.state.patient.id} />
          }
      </div>
    );
  }
}
