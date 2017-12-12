import React, { Component } from 'react';
import { Form } from '../Common';
import { State, parseError } from '../../Utils';
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
    onKeyPress: (o) =>{

    },
    maxlength: 10,
    validate: (o) => {
      console.log("0", o);
      console.log('o.length', o.length);
      //([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))
      var dt = moment(o);

      // Try to match full date.
      /*
      if(dt.isValid()) {
        console.log("1");
        return true;
      }
      */

      /*

      if(o.match(/(\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/)){
        console.log("2");
        return true;
      }

      if(o.match(/(\d{3}-([01])|(0[1-9]|1[0-2]))-/)){
        console.log("3");
        return true;
      }

      if(o.match(/(\d{3}-([01])|(0[1-9]|1[0-2]))/)){
        console.log("4");
        return true;
      }

      if(o.match(/(\d{3})-$/)){
        console.log("5");
        return true;
      }

      if(o.length <= 4 && !isNaN(o)) {
        console.log("7");

        return true;
      }

      */
      return !isNaN(o);

      console.log("here");

      return false;
      //return !isNaN(o);

    },
    format: (val) => {
      console.log('val', val);
      return val;
    }
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
