import React, { Component } from 'react';

import { browserHistory } from 'react-router';
import { PatientService } from '../../../../Services/HttpServices/PatientService';

import { Profile } from '../../Patient';
import { Staff } from './Staff';
import { Facilities } from './Facilities';

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
        <Staff id={this.props.id} />
        <Facilities id={this.props.id} />
      </div>
    );
  }
}
