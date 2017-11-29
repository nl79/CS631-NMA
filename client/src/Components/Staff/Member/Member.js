import React, { Component } from 'react';
import { Person } from '../../Person';

import { PersonService } from '../../../Services/HttpServices/PersonServices';

import { Form } from '../../Common';

const fields = [
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"firstName",
    label:"First Name",
    placeholder: 'First Name..'
  },
  {
    name:"type",
    label:"Staff Type",
    value:"nurse",
    type:"select",
    options:['physician', 'nurse', 'surgeon'],
    default: 'nurse'
  }
];

export class Member extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  componentWillReceiveProps(props) {

    let id = props.id || props.routeParams.id;
    if(!id) {
      this.setState({id: null});
    }
  }

  componentWillMount() {
    // Check if an id was supplied
    let id = this.props.id || this.props.routeParams.id;

    if(id) {
      StaffService.get(id).then((res) => {
        this.setState({
          ...res.data
        });
      })
    }
  }

  onSubmit(fields) {
    StaffService.save(fields)
      .then((res) => {
        this.setState({
          ...res.data
        });
      });
  }

  onChange(fields) {
    this.setState({
      ...this.state,
      ...fields
    });
  }

  onPersonSubmit(fields) {

    this.setState(
      {
        ...this.state,
        id: fields.id
      }
    );
  }

  onPersonChange(fields) {
    //console.log('onPersonChange', fields);

  }

  renderStaffData(id) {
    if(!id) { return null }

    return (
      <div>
        <Form title="Staff Information"
              fields={fields}
              data={this.state}
              onChange={ this.onChange.bind(this) }
              onSubmit={ this.onSubmit.bind(this) } />
      </div>
    )
  }

  render() {
    return (
      <div>
        <h2>Staff Information</h2>
        <Person
          key={this.state.id}
          id={this.state.id}
          onSubmit={ this.onPersonSubmit.bind(this) } />

        { this.renderStaffData(this.state.id) }
      </div>
    );
  }
}
