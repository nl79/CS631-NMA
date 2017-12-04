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


  renderRow(fields) {
    return (
      <div className="row">
        {
          fields.map((o) => {
            return (
              <div className="col-md-3">
                <div className="form-group">
                  <label for="exampleInputEmail1">{o.label}</label>
                  <div>
                    <span className="form-control-static"
                          id="exampleInputEmail1">{o.value}</span>
                  </div>
                </div>
              </div>
            )
          })
        }
      </div>
    );
  }

  render() {
    if(!this.state.data) {
      return null;
    }

    const d = this.state.data;

    return (
      <div className="panel panel-default">
        <div className="panel-heading">
          Patient Profile
          <button type="button" className="btn btn-primary pull-right">Edit</button>
        </div>
          <div className="panel-body">
            {this.renderRow([
              {label: 'First Name', value: d.firstName},
              {label: 'Last Name', value: d.lastName},
              {label: 'Date of Birth', value: d.dob},
              {label: 'Gender', value: d.gender}
            ])}

            {this.renderRow([
              {label: 'SSN', value: d.ssn},
              {label: 'Primary Phone:', value: d.phnumb},
              {label: 'Date of Admission', value: d.admin_date},
              {label: 'Blood Type', value: d.blood_type}
            ])}

            {this.renderRow([
              {label: 'Patient Number:', value: d.pnum},
              {label: 'Blood Sugar:', value: d.blood_sugar},
              {label: 'Blood Type', value: d.blood_type},
              {label: 'Cholesterol', value: d.cholesterol}
            ])}

        </div>
      </div>
    );
  }
}
