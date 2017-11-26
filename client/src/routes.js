import React from 'react';
import { Route, IndexRoute } from 'react-router';
import App from './Components/app';
import Dashboard from './Components/Dashboard/Dashboard';

// patients
import * as Patients from './Components/Patients/';

// staff
import * as Staff from './Components/Staff';

// Medication
import * as Medication from './Components/Medication';

// Medication
import * as Facilities from './Components/Facilities';

export default (
  <Route path='/' component={App}>
    <IndexRoute component={Dashboard} />
    <Route path='/patients'>
      <IndexRoute component={Patients.Dashboard} />
      <Route path='/patients/list' component={Patients.List} />
      <Route path='/patients/new' component={Patients.Patient} />
      <Route path='/patients/:id/edit' component={Patients.Patient} />
    </Route>
    <Route path='/staff'>
      <IndexRoute component={Staff.Dashboard} />
      <Route path='/staff/list' component={Staff.List} />
      <Route path='/staff/new' component={Staff.Member} />
      <Route path='/staff/:id/edit' component={Staff.Member} />
    </Route>
    <Route path='/medication'>
      <IndexRoute component={Medication.Dashboard} />
      <Route path='/medication/list' component={Medication.List} />
      <Route path='/medication/new' component={Medication.Medication} />
      <Route path='/medication/:id/edit' component={Medication.Medication} />
    </Route>
    <Route path='/facilities'>
      <IndexRoute component={Facilities.Dashboard} />
      <Route path='/facilities/list' component={Facilities.List} />
      <Route path='/facilities/room/new' component={Facilities.Room} />
      <Route path='/facilities/:id/edit' component={Facilities.Room} />
    </Route>
  </Route>
);
