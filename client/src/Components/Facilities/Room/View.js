import React, { Component } from 'react';
import { Room } from './Room';
import { Beds } from '../Bed';

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

  onRoomSubmit(fields) {

    this.setState(
      {
        ...this.state,
        id: fields.id
      }
    );
  }

  onConditionSubmit(fields) {

  }

  render() {
    return (
      <div>
        <h2>Room Information</h2>
        <Room
          id={this.state.id}
          onSubmit={ this.onRoomSubmit.bind(this) } />

        {
          this.state.id ?
          <Beds
            id={this.state.id}
            onSubmit={ this.onConditionSubmit.bind(this) } /> : null
        }

      </div>
    )
  }

}
