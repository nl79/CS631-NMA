import React, { Component } from 'react';
import { Form } from '../../Common';

import { State, parseError } from '../../../Utils';

import { StaffService } from '../../../Services/HttpServices/StaffService';

/*
  id									int																        not null,
  `role`							enum('nurse', 'surgeon', 'physician')			not null,
  `type`							enum('salary', 'contract')								not null,
  compensation				double(9,2)													      not null default 00.00,
  start_date					date																      not null,
  duration						double															      not null default 0.00,
*/

const fields = [
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"snum",
    label:"Staff Number",
    placeholder: 'Staff Number...',
    disabled: true
  },
  {
    name:"type",
    label:"Type",
    value:"salary",
    type:"select",
    options:['salary', 'contract'],
    default: 'salary',
    placeholder: 'Staff Type...',
    onChange: (e)=> {
      console.log('ontypeChange',e);
    }
  },
  /*
  {
    name:"duration",
    label:"Duration",
    type:"number",
    placeholder: 'Duration...',
    maxlength: 6
  },
  */
  {
    name:"role",
    label:"Role",
    value:"nurse",
    type:"select",
    options:['nurse', 'surgeon', 'physician'],
    default: 'nurse',
    placeholder: 'Staff Role...'
  },
  {
    name:"compensation",
    label:"Compensation",
    type:"number",
    placeholder: '$0.00',
    default: '0.00',
    maxlength: 6
  },
  {
    name:"start_date",
    label:"Start Date",
    type:"date",
    placeholder: 'Start Date...'
  }
];

export class Member extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      StaffService.get(id).then((res) => {

        let data;
        if(Array.isArray(res.data) && res.data.length){
          data = res.data[0];
        } else {
          data = res.data;
        }

        this.setState({data: {...this.state.data, ...data}}, (o) => {
          if(this.props.onLoad) {
            this.props.onLoad(this.state.data);
          }
        });
      });
    }
  }

  componentWillMount() {

    this.fetch(this.props.id);

  }

  componentWillReceiveProps(props) {


    if(!props.id){
      this.setState((e) => {
        return {data: {...State.reset(e.data)}}
      });
    }
    // if person data has not been loaded, or does not exist. fetch it.
    if(props.id !== this.state.data.id) {
      this.setState({data: {id: props.id}});
      this.fetch(props.id);
    }
  }

  onSubmit(fields) {
    // Save the person object.
    StaffService.save(fields)
      .then((res) => {

        if(res.data.id) {
          this.setState({data: {...res.data}, error: ''});
          if(this.props.onSubmit) {
            this.props.onSubmit(res.data);
          }
        } else {
          // Report Error
        }

      }).catch((e) => {
        let result = parseError(e.response.data);
        this.setState({data: {...fields}, error: result});
      })
  }

  onDelete(fields) {
    console.log('onDelete', fields);
  }

  render() {
    if(!this.state.data.id) {
      return null;
    }

    return (
      <Form
        title="Staff Member Information"
        fields={fields}
        error={this.state.error}
        data={this.state.data}
        onSubmit={ this.onSubmit.bind(this) }/>
    );
  }
}
