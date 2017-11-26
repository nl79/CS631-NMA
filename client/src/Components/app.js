import React, { Component } from 'react';

import Navbar from './Navbar';
import SideNav from './SideNav';

export default class App extends Component {
  render() {
    return (
      <div>
        <Navbar />
        <div className='container-fluid'>
          <div className='row'>
            <div className='col-sm-3'>
              <SideNav />
            </div>
            <div className='col-sm-9'>
              { this.props.children }
            </div>
          </div>
        </div>

      </div>
    );
  }
}
