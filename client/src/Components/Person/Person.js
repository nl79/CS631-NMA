import React, { Component } from 'react';
import { Form } from '../Common';
import { State, parseError, Datetime } from '../../Utils';
import moment from 'moment';



import { PersonService } from '../../Services/HttpServices/PersonServices';

const fields = [
  {
    name:"id",
    label:"id",
    type:"hidden",
    placeholder: 'id'
  },
  {
    name:"firstName",
    label:"First Name",
    placeholder: 'First Name..'
  },
  {
    name:"lastName",
    label:"Last Name",
    placeholder: 'Last Name..'
  },
  {
    name:"ssn",
    label:"SSN",
    placeholder: 'SSN..',
    type: "number",
    maxlength: 9

  },
  {
    name:"dob",
    label:"Date of Birth",
    type: "date",
    placeholder: 'Date of Birth(YYYY-MM-DD)..',
    maxlength: 10,
    validate: Datetime.date
  },
  {
    name:"phnumb",
    label:"Phone Number",
    placeholder: 'Phone Number..',
    type: "number",
    maxlength: 10
  },
  {
    name:"gender",
    label:"Gender",
    value:"n/a",
    type:"select",
    options:['n/a', 'm', 'f'],
    default: 'n/a'
  }
];

export class Person extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: '',
      error: ''
    };

  }

  fetchPerson(id) {
    if(id) {
      PersonService.get(id).then((res) => {
        this.setState({data: {...res.data}}, (o) => {
          if(this.props.onLoad) {
            this.props.onLoad(this.state.data);
          }
        });
      });
    }
  }

  componentWillMount() {
    this.fetchPerson(this.props.id);
  }

  componentWillReceiveProps(props) {

    if(!props.id){
      this.reset();
    }
    // if person data has not been loaded, or does not exist. fetch it.
    if(props.id !== this.state.data.id) {
      this.fetchPerson(props.id);
    }

  }

  reset() {
    this.setState((e) => {
      return {data: {...State.reset(e.data)}}
    });
  }

  onSubmit(fields) {
    // Save the person object.
    PersonService.save(fields)
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

  onChange(fields) {
    if(this.props.onChange) {
      this.props.onChange(fields);
    }
  }

  onReset() {

  }

  onDelete(fields) {
    if(this.props.onDelete) {
      if(this.props.onDelete(fields) === true) {
        this.delete(fields);
      }
    } else {
      this.delete(fields);
    }
  }

  delete(fields) {
    return PersonService.delete(fields.id).then((res) => {
      if(this.props.onDeleteSuccess) {
        this.props.onDeleteSuccess(fields);
      }
    });
  }

  render() {
    return (
      <Form
        title="Personal Information"
        fields={fields}
        data={this.state.data}
        error={this.state.error}
        onDelete={ this.onDelete.bind(this) }
        onSubmit={ this.onSubmit.bind(this) }
        onChange={ this.onChange.bind(this) }/>
    );
  }
}
