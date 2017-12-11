import React, { Component } from 'react';
export class View extends Component {

  componentWillMount() {

  }

  render() {
    return (
      <div>
        { this.props.children }
      </div>
    );
  }
}
