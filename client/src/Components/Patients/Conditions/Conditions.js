import React, { Component } from 'react';

import { PersonService } from '../../../Services/HttpServices/PersonServices';

import { Condition } from './Condition';
import { List } from './List';

export class Conditions extends Component {
  constructor(props) {
    super(props);

  }

  fetch(id) {

  }

  componentWillMount(props) {
    console.log('componentDidRecieveProps', props);

  }

  onConditionSubmit(params) {

  }

  onListUpdate(params) {

  }

  render() {
    return (
      <div>
        <h4>Conditions</h4>
        <Condition onSubmit={this.onConditionSubmit.bind(this)}/>
        <List />
      </div>
    );
  }
}
