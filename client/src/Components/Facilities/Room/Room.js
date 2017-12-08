import React, { Component } from 'react';
import { Form } from '../../Common';
import { State } from '../../../Utils';

import { FacilitiesService } from '../../../Services/HttpServices/FacilitiesService';

const fields = [
  {
    name:"id",
    label:"id",
    type:"number",
    placeholder: 'id',
    disabled: true
  },
  {
    name:"number",
    label:"Room Number",
    placeholder: 'Room Number...',
    type:"number",
    maxlength: '4'
  },
  {
    name:"desription",
    label:"Room Desription",
    placeholder: 'Room Desription...',
    type:"text",
    maxlength: 250
  },
  {
    name:"type",
    label:"Room Type",
    value:"recovery",
    type:"select",
    options:['office', 'surgery', 'recovery', 'emergency'],
    default: 'recovery'
  }
];

export class Room extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      FacilitiesService.getRoom(id).then((res) => {
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
    console.log('Room#componentWillMount', this);
    this.fetch(this.props.id);
  }

  componentWillReceiveProps(props) {

    console.log('Room#componentWillReceiveProps', props);

    if(!props.id){
      this.setState((e) => {
        return {data: {...State.reset(e.data)}}
      });
    }

    if(props.id !== this.state.data.id) {
      this.setState({data: {id: props.id}});
      this.fetch(props.id);
    }
  }

  onSubmit(fields) {
    // Save the person object.
    FacilitiesService.saveRoom(fields)
      .then((res) => {
        if(res.data.id) {
          this.setState({data: {...res.data}}, (o) => {
            if(this.props.onSubmit) {
              this.props.onSubmit(this.state.data);
            }
          });
        }
      });
  }

  render() {
    return (
      <Form
        title=""
        fields={fields}
        data={this.state.data}
        onSubmit={ this.onSubmit.bind(this) } />
    );
  }
}
