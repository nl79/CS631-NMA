import React, { Component } from 'react';

import { SchedulingService } from '../../../Services/HttpServices/SchedulingService';
import { browserHistory } from 'react-router';
import { Table } from '../../Common/Table';
import { SearchBar } from '../../Common/SearchBar';
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

    // Check if a custom fetch method is provided/
    if(this.props.fetch) {
      result = this.props.fetch();
    } else {
      result = SchedulingService.search();
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

  onSearch(q) {

    SchedulingService.search(q).then(res => {
      console.log('serach#res', res);
      this.setState({
        ...this.state,
        list: res.data || []
      });

    });
  }

  render() {
    return (
      <div>
        <h5>{ this.props.title || 'Appointment List' }</h5>

        {this.props.search !== false ?
          (<SearchBar onSubmit={this.onSearch.bind(this)}/>) : null

        }

        <Table
          className={this.props.className || ''}
          data={this.state.list}
          fields={this.props.fields}
          onSelect={this.onRowClick.bind(this)}/>

      </div>
    );
  }
}
