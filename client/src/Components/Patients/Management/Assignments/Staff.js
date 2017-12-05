import React, { Component } from 'react';

import { browserHistory } from 'react-router';
import { PatientService } from '../../../../Services/HttpServices/PatientService';

import { List } from '../../../Staff';
import { Table } from '../../../Common';

export class Staff extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: [],
      error: ''
    };
  }

  fetch(id) {
    return PatientService.staff(id).then((res) => {
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
    return PatientService.assignStaff(this.props.id, o.id)
      .then((res) => {
        return this.fetch(this.props.id);
      });
  }

  removeStaff(o) {
    return PatientService.unassignStaff(this.props.id, o.id)
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
              onSelect={this.removeStaff.bind(this)}
              data={this.state.data}/>

          </div>
          <div className='col-md-6'>
            <List onSelect={this.addStaff.bind(this)}
                  fields={['snum', 'firstName', 'lastName', 'role']}
                  autoFetch={false}
                  fetch={(args) => {
                    return PatientService.unassignedStaff(this.props.id);
                  } } />
          </div>
        </div>

      </div>
    );
  }
}
