import React, { Component } from 'react';

import { SkillService } from '../../../Services/HttpServices/SkillService';


import { Form } from '../../Common';

const fields = [
  //{ id:"name", label:"Client Name" },
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"name",
    label:"Name",
    placeholder: 'Skill Name..'
  },
];


export class Condition extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };
  }

  componentWillMount() {
    this.fetchTypes();
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
          Add Skill
        </h6>

        <Form

          className=''
          title=''
          fields={fields}
          onSubmit={this.onSubmit.bind(this)}/>

      </div>
    );
  }
}
