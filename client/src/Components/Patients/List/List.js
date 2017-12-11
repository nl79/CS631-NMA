import React, { Component } from 'react';

import { PatientService } from '../../../Services/HttpServices/PatientService';

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
    PatientService.list().then(res => {
      this.setState({
        ...this.state,
        list: res.data || []
      });
    })

  }
  onSearch(q) {

    PatientService.search(q).then(res => {
      console.log('serach#res', res);
      this.setState({
        ...this.state,
        list: res.data || []
      });

    });
  }

  onRowClick(o) {

    if(this.props.onSelect) {
      this.props.onSelect(o);
    } else {
      browserHistory.push(`/patients/${o.id}/view`);
    }
  }

  render() {
    return (
      <div>
        <h5>{ this.props.title || 'Patient List' }</h5>

        <SearchBar onSubmit={this.onSearch.bind(this)}/>


        <Table
          data={this.state.list}
          fields={this.props.fields}
          onSelect={this.onRowClick.bind(this)}/>

      </div>
    );
  }
}
