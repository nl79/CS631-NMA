import React, { Component } from 'react';

export class Dashboard extends Component {
  render() {
    return (
      <div>
        Scheduling Dashboard
        {this.props.children}
      </div>
    );
  }
}
