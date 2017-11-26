import React, { Component } from 'react';

import { PatientService } from '../../../Services/HttpServices/PatientService';


export class List extends Component {
  constructor(props) {
    super(props);

    this.state = {
      query: ''
    };
  }

  componentWillMount() {
    /*
    PatientService.list().then(res => {
      console.log('res', res);
    })
    */

  }
  submit() {
    console.log('submit', this.state.query);

  }
  render() {
    return (
      <div>
        Medication List
        <div className='row'>
          <div className="col-lg-6">
            <div className="input-group">
              <input type="text" className="form-control" onChange={(e) => { this.setState({query: e.target.value}) } } value={ this.state.query } placeholder="Search for..."/>
              <span className="input-group-btn">
                <button className="btn btn-default" type="button" onClick={(e)=>{this.submit()}}>Go!</button>
              </span>
            </div>
          </div>
        </div>
      </div>
    );
  }
}
