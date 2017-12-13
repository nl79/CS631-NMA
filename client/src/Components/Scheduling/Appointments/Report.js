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
      type: '',
      list: []
    };
  }

  componentWillMount() {
  }

  componentWillReceiveProps(props) {

    let type = props.routeParams && props.routeParams.type || '';

    if(this.state.type !== type) {
      this.setState({
        type: type,
        list: []
      });
    }
  }

  onSelect(o, type) {
    SchedulingService.appointmentsBy(
      this.props.routeParams && this.props.routeParams.type || '', o.id)
      .then((res) => {
        this.setState({
          type: type,
          list: res.data
        });
      });
  }

  onApptSelect(o) {

  }

  renderList(type) {
    switch (type) {
      case 'patients':
        return <Patients onSelect={ (o) => { this.onSelect(o, type) } } />;
      case 'staff':
        return <Staff onSelect={ (o) => { this.onSelect(o, type) } } />;
      case 'rooms':
        return <Rooms onSelect={ (o) => { this.onSelect(o, type) } }/>;
    }
  }

  render() {
    return (
      <div>
        <h2>Appointment Reports</h2>
        {this.renderList(this.props.routeParams && this.props.routeParams.type || '')}

        <Table
          data={this.state.list || [] }
          onSelect={this.onApptSelect.bind(this)}/>
      </div>
    );
  }
}
