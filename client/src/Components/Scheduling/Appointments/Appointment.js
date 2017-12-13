import React, { Component } from 'react';
import { Form } from '../../Common';
import { State, Datetime } from '../../../Utils';

import { SchedulingService } from '../../../Services/HttpServices/SchedulingService';

const fields = [
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id',
    disabled: true
  },
  {
    name:"patient",
    label:"Patient ID",
    type:"hidden",
    placeholder: 'Patient ID...',
    disabled: true
  },
  {
    name:"type",
    label:"Type",
    value:"appointment",
    type:"select",
    options:[
      'surgery',
      'appointment'
      ],
    default: 'appointment'
  },
  {
    name:"date",
    label:"Appointment Date",
    type:"date",
    placeholder: 'YYYY-MM-DD',
    validate: Datetime.date
  },
  {
    name:"time",
    label:"Time",
    type:"time",
    placeholder: 'HH:MM'
  },
  {
    name:"description",
    label:"Description",
    type:"text",
    placeholder: 'Description...'
  },
];

export class Appointment extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      SchedulingService.appointment(id).then((res) => {
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
    if(this.props.id) {
      this.fetch(this.props.id);
    }
    else if(this.props.patient) {
      this.setState({data: {
        patient: this.props.patient
      }});
    }
  }

  componentWillReceiveProps(props) {
    if(!props.id){
      this.setState((e) => {
        return {data: {...State.reset(e.data)}}
      });
    }

    if(props.id !== this.state.data.id) {
      this.fetch(props.id);
    }
  }

  onSubmit(fields) {
    // Save the person object.
    SchedulingService.saveAppointment(fields)
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
