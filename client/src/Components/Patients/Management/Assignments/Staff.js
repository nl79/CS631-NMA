import React, { Component } from 'react';

import { browserHistory } from 'react-router';
import { PatientService } from '../../../../Services/HttpServices/PatientService';

export class Staff extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: [],
      error: ''
    };
  }

  fetch(id) {
    console.log('Staff#fetch', id);
    return PatientService.staff(id).then((res) => {
      console.log('res', res);
    });

  }

  componentWillMount() {
    console.log('Staff#componentWillMount', this);
    this.fetch(this.props.id);

  }

  componentWillReceiveProps(props) {
    console.log('Staff#componentWillReceiveProps', props);
    this.fetch(props.id);
  }

  render() {
    return (
      <div>
        <h4> Staff Assignments </h4>

      </div>
    );
  }
}
