import React, { Component } from 'react';

import { browserHistory } from 'react-router';

import { List } from '../List';
import { Assignments } from './Assignments';

export class Dashboard extends Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {

  }

  componentWillReceiveProps(props) {

  }

  onPatientSelect(o) {
    browserHistory.push(`/patients/${o.id}/management`);
  }

  render() {
    return (
      <div>
        <h2>Patient Management</h2>
          {
            !this.props.params || !this.props.params.id
              ? <List onSelect={this.onPatientSelect.bind(this)}/>
              : <Assignments id={this.props.params.id} />
          }
      </div>
    );
  }
}
