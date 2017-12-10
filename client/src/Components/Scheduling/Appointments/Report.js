import React, { Component } from 'react';
import { browserHistory} from 'react-router';

import { Table } from '../../Common/Table';
import { List as Staff } from '../../Staff/List';
import { List as Rooms } from '../../Facilities/List';
import { List as Patients } from '../../Patients/List';
import { SchedulingService } from '../../../Services/HttpServices/SchedulingService';

export class Report extends Component {
  constructor(props) {
    super(props);

    this.state = {
      patient: ''
    };
  }

  init() {

  }

  componentWillMount() {
    console.log('Report#componentWillMount', this);

  }

  componentWillReceiveProps(props) {
    console.log('Report#componentWillReceiveProps', props);

  }

  onSelect(o) {
    SchedulingService.appointmentsBy(
      this.props.routeParams && this.props.routeParams.type || '', o.id)
      .then((res) => {
        console.log('res', res);
      })
  }

  onApptSelect(o) {

  }

  renderList(type) {
    switch (type) {
      case 'patient':
        return (<Patients onSelect={this.onSelect.bind(this)} />)
      case 'staff':
        return (<Staff onSelect={this.onSelect.bind(this)} />)
      case 'room':
        return (<Rooms onSelect={this.onSelect.bind(this)} />)

    }
  }

  render() {
    return (
      <div>
        <h2>Appointment Reports</h2>
        {this.renderList(this.props.routeParams && this.props.routeParams.type || '')}

        <Table
          data={this.state.list || [] }
          fields={this.props.fields}
          onSelect={this.onApptSelect.bind(this)}/>
      </div>
    );
  }
}
