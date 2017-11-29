import React, { Component } from 'react';

import { PersonService } from '../../../Services/HttpServices/PersonServices';

import { Form } from '../../Common';

const fields = [
  //{ id:"name", label:"Client Name" },
  {
    name:"name",
    label:"Condition Name",
    placeholder: 'First Name..'
  },
  {
    name:"description",
    label:"Description",
    placeholder: 'Last Name..'
  },
  {
    name:"type",
    label:"Type",
    value:"illness",
    type:"select",
    options:['allergy', 'illness'],
    default: 'allergy'
  }
];


export class Condition extends Component {
  constructor(props) {
    super(props);

    this.state = {};
  }

  componentWillMount(props) {

  }

  onSubmit(params) {
    if(this.props.onSubmit) {
      this.props.onSubmit(params);
    }
  }

  render() {
    return (
      <div>
        <h6>
          Add Condition
        </h6>

        <Form className=''
          title=''
          fields={fields}
          onSubmit={this.onSubmit.bind(this)}/>

      </div>
    );
  }
}
