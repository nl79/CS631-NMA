import React, { Component } from 'react';
import { Person } from '../../Person';
import { Patient } from './Patient';
import { Form } from '../../Common';

import { Conditions } from '../Conditions';

export class View extends Component {
  constructor(props) {
    super(props);

    this.state = {};

  }

  componentWillReceiveProps(props) {
    let id = props.id || props.routeParams.id;
    if(!id) {
      this.setState({id: null}, () => {
      });
    }
  }

  componentWillMount() {
    // Check if an id was supplied
    let id = this.props.id || this.props.routeParams.id;

    if(id) {
      this.setState({id: id})
    }
  }

  onPersonSubmit(fields) {

    this.setState(
      {
        ...this.state,
        id: fields.id
      }
    );
  }

  onPatientSubmit(fields) {
    this.setState(
      {
        ...this.state,
        pnum: fields.pnum
      }
    );
  }

  onConditionSubmit(fields) {

  }

  render() {
    return (
      <div>
        <h2>Patient Information</h2>
        <Person
          id={this.state.id}
          onSubmit={ this.onPersonSubmit.bind(this) } />

        <Patient
          id={this.state.id}
          onSubmit={ this.onPatientSubmit.bind(this) }
          onLoad={ this.onPatientSubmit.bind(this) } />

        {
          this.state.pnum ?
          <Conditions
            id={this.state.id}
            onSubmit={ this.onConditionSubmit.bind(this) } /> : null
        }

      </div>
    )
  }

}
