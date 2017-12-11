import React, { Component } from 'react';

import { PersonService } from '../../../Services/HttpServices/PersonService';
import { SkillService } from '../../../Services/HttpServices/SkillService';

import { Skill } from './Skill';
import { List } from './List';

export class Skills extends Component {
  constructor(props) {
    super(props);

    this.state = {
      id: props.id || '',
      data: '',
      error: ''
    };
  }

  fetch(id) {
    if(id) {
      PersonService.skills(id).then((res) => {
        this.setState({data: res.data}, (o) => {
          console.log('this.state', this.state);
        })

      });
    }
  }

  componentWillMount() {
    this.fetch(this.props.id);
  }

  onSkillSubmit(params) {
    SkillService.save(params)
      .then((res) => {
        if(res.data.id) {
          return PersonService.addSkill(this.props.id, res.data.id);
        }
      })
      .then((res) => {
        //reload the conditions list.
        if(res.data.person) {
          return this.fetch(res.data.person);
        }
      });
  }

  onListUpdate(params) {

  }

  render() {
    if(!this.props.id) {
      return null;
    }

    return (
      <div>
        <h4>Conditions</h4>
        <Condition onSubmit={this.onConditionSubmit.bind(this)}/>
        <List list={this.state.data}/>
      </div>
    );
  }
}
