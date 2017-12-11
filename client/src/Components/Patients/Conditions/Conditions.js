import React, { Component } from 'react';

import { PatientService } from '../../../Services/HttpServices/PatientService';
import { ConditionService } from '../../../Services/HttpServices/ConditionService';

import { Condition } from './Condition';
import { List } from './List';

export class Conditions extends Component {
  constructor(props) {
    super(props);

    this.state = {
      id: props.id || '',
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      PatientService.conditions(id).then((res) => {
        this.setState({data: res.data}, (o) => {
          console.log('this.state', this.state);
        })

      });
    }
  }

  componentWillMount() {
    this.fetch(this.props.id);
  }

  onConditionSubmit(params) {
    ConditionService.save(params)
      .then((res) => {
        if(res.data.id) {
          return PatientService.addCondition(this.props.id, res.data.id);
        }
      })
      .then((res) => {
        //reload the conditions list.
        if(res.data.patient) {
          return this.fetch(res.data.patient);
        }
      });
  }

  onListUpdate(params) {

  }

  render() {
    if(!this.props.id) {
      return null;
    }

    return (
      <div>
        <h4>Conditions</h4>
        <Condition onSubmit={this.onConditionSubmit.bind(this)}/>
        <List list={this.state.data}/>
      </div>
    );
  }
}
