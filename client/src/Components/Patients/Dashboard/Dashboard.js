import React, { Component } from 'react';

import { Link } from 'react-router';

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
