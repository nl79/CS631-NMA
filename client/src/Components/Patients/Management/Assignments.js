import React, { Component } from 'react';

import { browserHistory } from 'react-router';
import { PatientService } from '../../../Services/HttpServices/PatientService';

import { Profile } from '../Patient';

export class Assignments extends Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {

  }

  componentWillReceiveProps(props) {

  }

  render() {
    return (
      <div>
        <Profile id={this.props.id} />
      </div>
    );
  }
}
