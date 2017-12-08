import React, { Component } from 'react';

import { FacilitiesService } from '../../../Services/HttpServices/FacilitiesService';
import { Table } from '../../Common/Table';
import { browserHistory } from 'react-router';

export class List extends Component {
  constructor(props) {
    super(props);

    this.state = {
      query: '',
      list: []
    };
  }

  componentWillMount() {
    console.log('Facilities$List#omponentWillMount#props', this.props);
    if(this.props.autoFetch !== false) {
      this.fetch();
    }

  }

  fetch(filter) {

    let result;

    // Check if a custom fetch method is provided/
    if(this.props.fetch) {
      result = this.props.fetch();
    } else {
      result = FacilitiesService.listRooms();
    }

    result.then(res => {
      this.setState({
        ...this.state,
        list: res.data || []
      });
    });
  }

  submit() {
    console.log('submit', this.state.query);

  }

  componentWillReceiveProps(props) {
    console.log('Facilities$ListcomponentWillReceiveProps#props', props);
    this.fetch();
  }

  onRowClick(o) {
    if(this.props.onSelect) {
      this.props.onSelect(o);
    } else {
      browserHistory.push(`/facilities/room/${o.id}/view`);
    }
  }

  render() {
    return (
      <div>
        <h5>Room List</h5>
        <div className='row'>
          <div className="col-lg-6">
            <div className="input-group">
              <input type="text" className="form-control" onChange={(e) => {
                this.setState({query: e.target.value})
              }} value={this.state.query} placeholder="Search for..."/>
              <span className="input-group-btn">
                <button className="btn btn-default" type="button" onClick={(e) => {
                  this.submit()
                }}>Go!</button>
              </span>
            </div>
          </div>
        </div>

        <Table
          data={this.state.list}
          fields={this.props.fields}
          onSelect={this.onRowClick.bind(this)}/>

      </div>
    );
  }
}
