import React, { Component } from 'react';
import { Person } from '../../Person';
import { Form } from '../../Common';

import { State } from '../../../Utils';

import { PatientService } from '../../../Services/HttpServices/PatientService';

const fields = [
  //{ id:"name", label:"Client Name" },
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"pnum",
    label:"Patient Number",
    placeholder: 'Patient Number...',
    disabled: true
  },
  {
    name:"blood_type",
    label:"Blood Type",
    value:"",
    type:"select",
    options:['o+', 'o-', 'a+', 'a-', 'b+', 'b-', 'ab+', 'ab-'],
    default: 'o+',
    placeholder: 'Blood Type...'
  },
  {
    name:"admit_date",
    label:"Date of Admission",
    type:"date",
    placeholder: 'Date of Admission...'
  },
  {
    name:"cholesterol",
    label:"Cholesterol",
    placeholder: 'Cholesterol...',
    type: "number",
    maxlength: 3
  },
  {
    name:"blood_sugar",
    label:"Blood Sugar",
    placeholder: 'Blood Sugar...',
    type: "number",
    maxlength: 3
  },
];

export class Patient extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      PatientService.get(id).then((res) => {

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
    PatientService.save(fields)
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
