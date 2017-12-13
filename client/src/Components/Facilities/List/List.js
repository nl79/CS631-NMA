import React, { Component } from 'react';

import { FacilitiesService } from '../../../Services/HttpServices/FacilitiesService';
import { Table } from '../../Common/Table';
import { browserHistory } from 'react-router';
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

  componentWillReceiveProps(props) {
    this.fetch();
  }

  onRowClick(o) {
    if(this.props.onSelect) {
      this.props.onSelect(o);
    } else {
      browserHistory.push(`/facilities/room/${o.id}/view`);
    }
  }

  onSearch(q) {

    let result;
    // Check if a custom fetch method is provided/
    if(this.props.search) {
      result = this.props.search(q);
    } else {
      result = FacilitiesService.listRooms({q});
    }

    result.then(res => {
      this.setState({
        ...this.state,
        list: res.data || []
      });
    });
  }

  render() {
    return (
      <div>
        <h5>{this.props.title || 'Room List'}</h5>

        <SearchBar onSubmit={this.onSearch.bind(this)}/>

        <Table className={this.props.className || ''}
          data={this.state.list}
          fields={this.props.fields}
          onSelect={this.onRowClick.bind(this)}/>

      </div>
    );
  }
}
