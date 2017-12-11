import React, { Component } from 'react';

import {StaffService} from "../../../Services/HttpServices/StaffService";
import {browserHistory} from "react-router";

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
    console.log('Staff$List#omponentWillMount#props', this.props);
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
      result = StaffService.search();
    }

    result.then(res => {
      this.setState({
        ...this.state,
        list: res.data || []
      });
    });
  }



  componentWillReceiveProps(props) {
    console.log('Staff$ListcomponentWillReceiveProps#props', props);
    this.fetch();
  }

  onRowClick(o) {
    if(this.props.onSelect) {
      this.props.onSelect(o);
    } else {
      browserHistory.push(`/staff/${o.id}/view`);
    }
  }

  onSearch(q) {
    let result;

    // Check if a custom fetch method is provided/
    if(this.props.search) {
      result = this.props.search(q);
    } else {
      result = StaffService.search(q);
    }

    result.then(res => {
      this.setState({
        ...this.state,
        list: res.data || []
      });
    });
/*
    StaffService.search(q).then(res => {
      console.log('serach#res', res);
      this.setState({
        ...this.state,
        list: res.data || []
      });

    });
    */
  }

  render() {
    return (
      <div>
        <h5>Staff List</h5>
        <SearchBar onSubmit={this.onSearch.bind(this)}/>

        <Table
          className={this.props.className || ''}
          data={this.state.list}
          fields={this.props.fields}
          onSelect={this.onRowClick.bind(this)}/>

      </div>
    );
  }
}
