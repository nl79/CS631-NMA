import React, { Component } from 'react';

import { PatientService } from '../../../Services/HttpServices/PatientService';

export class Profile extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      PatientService.profile(id).then((res) => {
        this.setState({data: res.data});
      });
    }
  }

  componentWillMount() {
    this.fetch(this.props.id);
  }

  componentWillReceiveProps(props) {
    // if person data has not been loaded, or does not exist. fetch it.
    if(props.id !== this.state.data.id) {
      this.fetch(props.id);
    }
  }

  render() {
    if(!this.state.data) {
      return null;
    }

    console.log('this.state.date', this.state.data);

    return (
      <div className="panel panel-default">
        <div className="panel-heading">Patient Profile</div>
        <div className="panel-body">
          
        </div>
      </div>
    );
  }
}
