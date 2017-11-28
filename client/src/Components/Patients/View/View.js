import React, { Component } from 'react';

import { PatientService } from '../../../Services/HttpServices/PatientService';

import { browserHistory } from 'react-router';


export class View extends Component {

  componentWillMount() {

  }

  render() {
    return (
      <div>
        { this.props.children }
      </div>
    );
  }
}
