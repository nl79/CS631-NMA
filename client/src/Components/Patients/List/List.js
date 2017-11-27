import React, { Component } from 'react';

import { PatientService } from '../../../Services/HttpServices/PatientService';

import { browserHistory } from 'react-router';


export class List extends Component {
  constructor(props) {
    super(props);

    this.state = {
      query: '',
      list: []
    };
  }

  componentWillMount() {
    console.log('componentWillMount');
    PatientService.list().then(res => {
      console.log('res', res);
      this.setState({
        ...this.state,
        list: res.data
      });
    })

  }
  submit() {
    console.log('submit', this.state.query);

  }

  onRowClick(o) {
    console.log('onrowclick', o);

    browserHistory.push(`/patients/${o.id}/view`);
  }
  render() {
    console.log('state', this.state);
    return (
      <div>
        Patient List
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

        <table className='table'>
          <thead>
          </thead>
          <tbody className='table-striped'>
            {
              this.state.list.map((o, i) => {
                let keys = Object.keys(o);

                return (
                  <tr key={i} onClick={ (e) => { this.onRowClick(o) }}>
                    {
                      keys.map((k, y) => {
                        return (
                          <td>{o[k]}</td>
                        )
                      })
                    }
                  </tr>
                )
              })
            }
          </tbody>
        </table>

      </div>
    );
  }
}
