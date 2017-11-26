import React, { Component } from 'react';

export class Input extends Component {
  constructor(props) {
    super(props);

    this.state = {
      value: ''
    };
  }

  onChange(e) {

    this.setState({value: e.target.value}, ()=> {
      if(this.props.onChange) {
        this.props.onChange(e.target.value);
      }
    });
  }
  
  render() {
    return (

      <input type="text"
          className={this.props.classNane || ''}
          onChange={ (e) => { this.onChange(e); }}
          value={this.state.value}
          placeholder={this.props.placeholder || ''}/>
    );
  }
}
