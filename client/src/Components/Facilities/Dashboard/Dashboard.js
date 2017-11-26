import React, { Component } from 'react';

export class Dashboard extends Component {
  render() {
    return (
      <div>
        Medication Dashboard
        {this.props.children}
      </div>
    );
  }
}
