import React, { Component } from 'react';

import { ConditionService } from '../../../Services/HttpServices/ConditionService';


import { Form } from '../../Common';

const fields = [
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"rnum",
    label:"Room Number",
    placeholder: 'Room Number...',
    type:"number",
    disabled: true
  },
  {
    name:"number",
    label:"Bed Number",
    placeholder: 'Room Number...',
    type:"number",
    maxlength: '4'
  },
  {
    name:"size",
    label:"Bed Size",
    value:"twin",
    type:"select",
    options:['twin', 'twin xl', 'full', 'queen', 'king'],
    default: 'twin'
  }
];


export class Bed extends Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    //console.log('Bed#componentWillMount', this);
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
          Add Bed
        </h6>

        <Form
          className='form-inline'
          title=''
          data={{rnum: this.props.rnum}}
          fields={fields}
          onSubmit={this.onSubmit.bind(this)}/>

      </div>
    );
  }
}
