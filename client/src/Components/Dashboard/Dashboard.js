import React, { Component } from 'react';
import { Link } from 'react-router';

let nav = [
  {
    to: '/patients',
    label: 'Patient Management',
    children: [
      {
        to: '/list',
        label: 'Patient List'
      },
      {
        to: '/new',
        label: 'Add Patient'
      },
      {
        to: '/management',
        label: 'In-patient Management - Admitted Patient Management'
      }
    ]
  },
  {
    to: '/staff',
    label: 'Staff Management',
    children: [
      {
        to: '/list',
        label: 'Staff List'
      },
      {
        to: '/new',
        label: 'Add Staff'
      }
    ]
  },
  {
    to: '/facilities',
    label: 'Facilities Management',
    children: [
      {
        to: '/list',
        label: 'Facilities List'
      },
      {
        to: '/room/new',
        label: 'Add Room'
      }
    ]
  },
  {
    to: '/scheduling',
    label: 'Schedule Management',
    children: [
      {
        to: '/appointments',
        label: 'Appointments',
        children: [
          {
            to: '/new',
            label: 'New Appointment'
          },
          {
            to: '/rooms/report',
            label: 'By Room - View Appointments Per Room per Day'
          },
          {
            to: '/staff/report',
            label: 'By Doctor - View Appointments Per Doctor per Day'
          },
          {
            to: '/patients/report',
            label: 'By Patient - - View Appointments Per Patient per Day'
          }
        ]
      },
      {
        to: '/shifts',
        label: 'Shifts',
        children: [
          {
            to: '/new',
            label: 'New Shift'
          }
        ]
      }
    ]
  },
];


export class SiteMap extends Component {
  render() {
    return (
      <Nav className={'nav nav-pills nav-stacked'} items={nav} />
    );
  }
}

export class Nav extends Component {
  render() {
    return (
      <ul className={this.props.className}>
        {
          this.props.items.map((o, i) => {
            let prefix = this.props.prefix ? this.props.prefix + o.to : o.to;
            return (
              <li key={i + o.to } role="presentation" className="active">
                <Link to={ prefix }>{ o.label }</Link>

                {
                  o.children && o.children.length ?  (<Nav prefix={ prefix } items={o.children} />) : null
                }
              </li>
            );
          })
        }
      </ul>
    );
  }
}

export default class Dashboard extends Component {
  render() {

    return (
      <div>
        Site-Map
        <Nav className={'nav nav-stacked'} items={nav} />
      </div>
    );
  }
}
