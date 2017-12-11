import React, { Component } from 'react';
import { Shift } from './Shift';
import { Staff } from './Staff';

export class View extends Component {
  constructor(props) {
    super(props);

    this.state = {};

  }

  componentWillReceiveProps(props) {
    //console.log('Room#vew#componentWillReceiveProps', props);
    let id = props.id || props.routeParams.id;
    if(!id) {
      this.setState({id: null}, () => {
      });
    }
  }

  componentWillMount() {
    //console.log('Room#vew#componentWillMount', this);

    // Check if an id was supplied
    let id = this.props.id || this.props.routeParams.id;

    if(id) {
      this.setState({id: id})
    }
  }

  onShiftSubmit(fields) {

    this.setState(
      {
        ...this.state,
        id: fields.id
      }
    );
  }

  render() {
    return (
      <div>
        <h2>Shift Information</h2>
        <Shift
          id={this.state.id}
          onSubmit={ this.onShiftSubmit.bind(this) } />

        {this.state.id ?
          <Staff id={this.state.id} /> : null }
      </div>
    )
  }

}
