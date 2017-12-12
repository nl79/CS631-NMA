import React, { Component } from 'react';
import { Person } from '../../Person';
import { Member } from './Member';
import { Form } from '../../Common';

import {browserHistory} from "react-router";

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

  onMemberSubmit(fields) {
    browserHistory.push('/staff/list');
  }

  onConditionSubmit(fields) {

  }

  onPersonDelete(fields) {
    return true;
  }

  onPersonDeleteSuccess(fields) {
    browserHistory.push('/staff/list');
  }
  render() {
    return (
      <div>
        <h2>Staff Information</h2>
        <Person
          id={this.state.id}
          onSubmit={ this.onPersonSubmit.bind(this) }
          onDelete={ this.onPersonDelete.bind(this) }
          onDeleteSuccess={ this.onPersonDeleteSuccess.bind(this) }
           />

        <Member
          id={this.state.id}
          onSubmit={ this.onMemberSubmit.bind(this) }/>

      </div>
    )
  }

}
