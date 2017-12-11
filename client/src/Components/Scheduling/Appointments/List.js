import React, { Component } from 'react';

import { SchedulingService } from '../../../Services/HttpServices/SchedulingService';
import { browserHistory } from 'react-router';
import { Table } from '../../Common/Table';

export class List extends Component {
  constructor(props) {
    super(props);

    this.state = {
      query: '',
      list: []
    };
  }

  componentWillMount() {
    /*
    SchedulingService.appointments().then(res => {
      this.setState({
        ...this.state,
        list: res.data || []
      });
    })
    */
    if(this.props.autoFetch !== false) {
      this.fetch();
    }

  }

  fetch(filter) {

    let result;

    console.log('fetch', this);

    // Check if a custom fetch method is provided/
    if(this.props.fetch) {
      result = this.props.fetch();
    } else {
      result = SchedulingService.appointments();
    }

    result.then(res => {
      this.setState({
        ...this.state,
        list: res.data || []
      });
    });
  }

  submit() {

    if(this.props.onSearch) {
      this.props.onSearch(o);
    } else {
        //perform search
    }
  }

  onRowClick(o) {
    if(this.props.onSelect) {
      this.props.onSelect(o);
    } else {
      browserHistory.push(`/scheduling/appointments/${o.id}/view`);
    }
  }

  render() {
    return (
      <div>
        { this.props.title || 'Appointment List' }

        {this.props.search === true ?
          (<div className='row'>
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
          </div>) : null

        }

        <Table
          data={this.state.list}
          fields={this.props.fields}
          onSelect={this.onRowClick.bind(this)}/>

      </div>
    );
  }
}
