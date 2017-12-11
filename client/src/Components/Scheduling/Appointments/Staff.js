import React, { Component } from 'react';

import { browserHistory } from 'react-router';
import { SchedulingService } from '../../../Services/HttpServices/SchedulingService';

import { List } from '../../Staff';
import { Table } from '../../Common';

export class Staff extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: [],
      error: ''
    };
  }

  fetch(id) {
    return SchedulingService.appointmentStaff(id).then((res) => {
      this.setState({...this.state, data: res.data});
    });

  }

  componentWillMount() {
    this.fetch(this.props.id);

  }

  componentWillReceiveProps(props) {
    //this.fetch(props.id);
  }

  addStaff(o) {
    return SchedulingService.assingAptStaff(this.props.id, o.id)
      .then((res) => {
        return this.fetch(this.props.id);
      });
  }

  removeStaff(o) {
    return SchedulingService.unassignAptStaff(this.props.id, o.id)
      .then((res) => {
        return this.fetch(this.props.id);
      });
  }

  render() {
    return (
      <div>
        <h4> Staff Assignments </h4>
        <div className='row'>
          <div className='col-md-6'>
            <h5>Currently Assigned Staff</h5>
            <Table
              className='minus'
              onSelect={this.removeStaff.bind(this)}
              fields={['snum', 'firstName', 'lastName', 'role']}
              data={this.state.data || []}/>

          </div>
          <div className='col-md-6'>
            <List className='plus'
                  onSelect={this.addStaff.bind(this)}
                  fields={['snum', 'firstName', 'lastName', 'role']}
                  autoFetch={false}
                  search={(q) => {
                    return SchedulingService.unassignedAptStaff(this.props.id, {q});
                  } }
                  fetch={(args) => {
                    return SchedulingService.unassignedAptStaff(this.props.id);
                  } } />
          </div>
        </div>

      </div>
    );
  }
}
