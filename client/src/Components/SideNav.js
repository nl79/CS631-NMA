import React, { Component } from 'react';
import { Link } from 'react-router';

let nav = [
  {
    to: '/',
    label: 'Dashboard',

  },
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
    to: '/medication',
    label: 'Medication Management',
    children: [
      {
        to: '/list',
        label: 'Medication List'
      },
      {
        to: '/new',
        label: 'Add Medication'
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
  }
];

export default class SideNav extends Component {
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
            return (
              <li key={i + o.to } role="presentation" className="active">
                <Link to={ this.props.prefix ? this.props.prefix + o.to : o.to }>{ o.label }</Link>

                {
                  o.children && o.children.length &&  (<Nav prefix={ o.to} items={o.children} />)
                }
              </li>
            );
          })
        }
      </ul>
    );
  }
}
