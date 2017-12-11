import React, { Component } from 'react';

export class Table extends Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {

  }
  submit() {

  }

  onRowClick(o) {
    if(this.props.onSelect) {
      this.props.onSelect(o);
    }
  }

  render() {
    return (
      <table className='table'>
        <thead>
        </thead>
        <tbody className='table-striped'>
        {
          this.props.data.map((o, i) => {
            let keys = this.props.fields || Object.keys(o);

            return (
              <tr key={i} onClick={(e) => {
                this.onRowClick(o)
              }}>
                {
                  keys.map((k, y) => {
                    return (
                      <td key={y}>{o[k]}</td>
                    )
                  })
                }
              </tr>
            )
          })
        }
        </tbody>
      </table>
    )
  }
}
