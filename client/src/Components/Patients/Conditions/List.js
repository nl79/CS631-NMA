import React, { Component } from 'react';

import { PersonService } from '../../../Services/HttpServices/PersonServices';

import { Condition } from './Condition';

export class List extends Component {
  constructor(props) {
    super(props);
  }

  onRowClick(o) {
  }

  render() {
    if(!this.props.list || !this.props.list.length) {
      return null;
    }

    return (
      <div>
        <h4>List</h4>
        <table className='table'>
          <thead>
          </thead>
          <tbody className='table-striped'>
            {
              this.props.list.map((o, i) => {
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
