import React, { Component } from 'react';
import { Form } from '../../Common';
import { State, Datetime } from '../../../Utils';

import { SchedulingService } from '../../../Services/HttpServices/SchedulingService';

const fields = [
  {
    name:"id",
    label:"id",
    type:"number",
    placeholder: 'id',
    disabled: true
  },
  {
    name:"type",
    label:"Shift Type",
    value:"1",
    type:"select",
    options:[
        {key: '1', value: '1st : 12:00 AM - 8:00 AM' },
        {key: '2', value: '2nd : 8:00 AM - 4:00 PM' },
        {key: '3', value: '3rd : 4:00 PM - 12:00 AM'}
      ],
    default: '1'
  },
  {
    name:"date",
    label:"Shift Date",
    type:"date",
    placeholder: 'YYYY-MM-DD',
    validate: Datetime.date
  }
];

export class Shift extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      SchedulingService.getShift(id).then((res) => {
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

    if(props.id !== this.state.data.id) {
      this.setState({data: {id: props.id}});
      this.fetch(props.id);
    }
  }

  onSubmit(fields) {
    // Save the person object.
    SchedulingService.saveShift(fields)
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
