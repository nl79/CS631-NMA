import React, { Component } from 'react';

export class Dashboard extends Component {
  render() {
    return (
      <div>
        Patient Dashboard
        {this.props.children}
      </div>
    );
  }
}
