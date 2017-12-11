import React, { Component } from 'react';

import { ConditionService } from '../../../Services/HttpServices/ConditionService';


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
  }
  /*
  {
    name:"type",
    label:"Type",
    value:"illness",
    type:"select",
    options:['allergy', 'illness'],
    default: 'allergy'
  }
  */
];


export class Condition extends Component {
  constructor(props) {
    super(props);

    this.state = {
      types: [],
      fields: []
    };
  }

  fetchTypes() {
    ConditionService.types().then((res) => {

      let fields = [
        //{ id:"name", label:"Client Name" },
        {
          name:"name",
          label:"Condition Name",
          placeholder: 'Condition Name..'
        },
        {
          name:"description",
          label:"Description",
          placeholder: 'Description..'
        }
      ]

      let options = res.data.map((o) => {
          return {key: o.id, value: o.name}
        });

      // Build an options field and update.

      fields.unshift(
        {
          name:"type",
          label:"Type",
          value:options[0].key || '',
          type:"select",
          options:options,
          default: options[0].key || ''
        }
      );

      this.setState({fields: fields, types: res.data});
    })
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
          Add Condition
        </h6>

        <Form

          className='form-inline'
          title=''
          fields={this.state.fields}
          onSubmit={this.onSubmit.bind(this)}/>

      </div>
    );
  }
}
