import React, { Component } from 'react';

import { List as Appointments } from '../Appointments';
import { List as Shifts } from '../Shifts';

export class Dashboard extends Component {
  render() {
    return (
      <div>
        Scheduling Dashboard
        <Appointments search={true}/>
        <Shifts />

      </div>
    );
  }
}
