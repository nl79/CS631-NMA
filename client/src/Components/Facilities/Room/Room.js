import React, { Component } from 'react';
import { Person } from '../../Person';
import { Form } from '../../Common';

import { State } from '../../../Utils';

import { FacilitiesService } from '../../../Services/HttpServices/FacilitiesService';

const fields = [
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"pnum",
    label:"Room Number",
    placeholder: 'Room Number...',
    disabled: true
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
      FacilitiesService.get(id).then((res) => {

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

  init(id) {
    this.fetch(id);
  }

  componentWillMount() {
    this.init(this.props.id);
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
    FacilitiesService.save(fields)
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
    if(!this.state.data.id) {
      return null;
    }

    return (
      <Form
        title="Patient Information"
        fields={fields}
        data={this.state.data}
        onSubmit={ this.onSubmit.bind(this) } />
    );
  }
}
