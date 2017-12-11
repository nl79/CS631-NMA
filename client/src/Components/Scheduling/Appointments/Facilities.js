import React, { Component } from 'react';

import { List } from '../../Facilities';
import { Table } from '../../Common';

import { SchedulingService } from '../../../Services/HttpServices/SchedulingService';

export class Facilities extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: [],
      error: ''
    };
  }

  fetch(id) {
    return SchedulingService.appointmentRooms(id).then((res) => {
      this.setState({...this.state, data: res.data});
    });

  }

  componentWillMount() {
    this.fetch(this.props.id);

  }

  componentWillReceiveProps(props) {
    //this.fetch(props.id);
  }

  addRoom(o) {
    return SchedulingService.assingAptRoom(this.props.id, o.id)
      .then((res) => {
        return this.fetch(this.props.id);
      });
  }

  removeRoom(o) {
    return SchedulingService.unassignAptRoom(this.props.id, o.id)
      .then((res) => {
        return this.fetch(this.props.id);
      });
  }

  render() {
    return (
      <div>
        <h4> Facilities Assignments </h4>
        <div className='row'>
          <div className='col-md-6'>
            <h5>Currently Assigned Rooms</h5>
            <Table
              className='minus'
              onSelect={this.removeRoom.bind(this)}
              data={this.state.data || []} />

          </div>
          <div className='col-md-6'>
            <List className='plus'
                  onSelect={this.addRoom.bind(this)}
                  autoFetch={false}
                  search={(q) => {
                    return SchedulingService.unassignedAptRooms(this.props.id, {q});
                  } }
                  fetch={(args) => {
                    return SchedulingService.unassignedAptRooms(this.props.id);
                  } } />
          </div>
        </div>

      </div>
    );
  }
}
