import React, { Component } from 'react';

import { Bed } from './Bed';
import { List } from './List';

import { FacilitiesService } from '../../../Services/HttpServices/FacilitiesService';


export class Beds extends Component {
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
      FacilitiesService.getBeds(id).then((res) => {
        this.setState({data: res.data}, (o) => {
          console.log('this.state', this.state);
        })

      });
    }
  }

  componentWillMount() {
    this.fetch(this.props.id);
  }

  onBedSubmit(params) {

    FacilitiesService.addBed(this.props.id, params)
      .then((res) => {
        //reload the bed list.
        if(res.data.id) {
          return this.fetch(this.props.id);
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
        <h4>Beds</h4>
        <Bed rnum={this.props.id}
             onSubmit={this.onBedSubmit.bind(this)}/>
        <List list={this.state.data}/>
      </div>
    );
  }
}
