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
    console.log('Conditions#componentDidRecieveProps', props);

  }

  onConditionSubmit(params) {

    console.log('onConditionSubmit#params', params);

  }

  onListUpdate(params) {

  }

  render() {
    if(!this.props.patient) {
      return null;
    }

    return (
      <div>
        <h4>Conditions</h4>
        <Condition onSubmit={this.onConditionSubmit.bind(this)}/>
        <List />
      </div>
    );
  }
}
