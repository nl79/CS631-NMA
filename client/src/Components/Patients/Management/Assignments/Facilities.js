import React, { Component } from 'react';

import { List } from '../../../Facilities';
import { Table } from '../../../Common';

import { FacilitiesService } from '../../../../Services/HttpServices/FacilitiesService';
import { PatientService } from '../../../../Services/HttpServices/PatientService';

export class Facilities extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: [],
      error: ''
    };
  }

  fetch(id) {
    return PatientService.beds(id).then((res) => {
      this.setState({...this.state, data: res.data});
    });

  }

  componentWillMount() {
    this.fetch(this.props.id);

  }

  componentWillReceiveProps(props) {
    //this.fetch(props.id);
  }

  addBed(o) {
    return PatientService.assignBed(this.props.id, o.id)
      .then((res) => {
        return this.fetch(this.props.id);
      });
  }

  removeBed(o) {
    return PatientService.unassignBed(this.props.id, o.id)
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
            <h5>Currently Assigned Bed</h5>
            <Table
              className='minus'
              onSelect={this.removeBed.bind(this)}
              fields={['number', 'rnum', 'size', 'type']}
              data={this.state.data || []} />

          </div>
          <div className='col-md-6'>
            <List className='plus'
                  title={'Bed/Room List'}
                  onSelect={this.addBed.bind(this)}
                  fields={['number', 'rnum', 'size', 'type']}
                  autoFetch={false}
                  search={(q) => {
                    return PatientService.unassignedBeds(this.props.id, {q});
                  }}
                  fetch={(args) => {
                    return PatientService.unassignedBeds(this.props.id);
                  } } />
          </div>
        </div>

      </div>
    );
  }
}
