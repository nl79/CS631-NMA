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

// Facilities
import * as Facilities from './Components/Facilities';

// Scheduling
import * as Scheduling from './Components/Scheduling';

export default (
  <Route path='/' component={App}>
    <IndexRoute component={Dashboard} />
    <Route path='/patients' component={Patients.View}>
      <IndexRoute component={Patients.Dashboard} />
      <Route path='/patients/list' component={Patients.List} />
      <Route path='/patients/new' component={Patients.Patient.View} />
      <Route path='/patients/:id/view' component={Patients.Patient.View} />
      <Route path='/patients/management' component={Patients.Management.Dashboard} />
      <Route path='/patients/:id/management' component={Patients.Management.Dashboard} />
    </Route>
    <Route path='/staff'>
      <IndexRoute component={Staff.Dashboard} />
      <Route path='/staff/list' component={Staff.List} />
      <Route path='/staff/new' component={Staff.Member.View} />
      <Route path='/staff/:id/view' component={Staff.Member.View} />
    </Route>
    <Route path='/medication'>
      <IndexRoute component={Medication.Dashboard} />
      <Route path='/medication/list' component={Medication.List} />
      <Route path='/medication/new' component={Medication.Medication} />
      <Route path='/medication/:id/view' component={Medication.Medication} />
    </Route>
    <Route path='/facilities'>
      <IndexRoute component={Facilities.Dashboard} />
      <Route path='/facilities/list' component={Facilities.List} />
      <Route path='/facilities/room/new' component={Facilities.Room.View} />
      <Route path='/facilities/room/:id/view' component={Facilities.Room.View} />
    </Route>
    <Route path='/scheduling'>
      <IndexRoute component={Scheduling.Dashboard} />
      <Route path='/scheduling/list' component={Scheduling.Dashboard} />
      <Route path='/scheduling/shifts' component={Scheduling.Shifts.List} />
      <Route path='/scheduling/shifts/new' component={Scheduling.Shifts.View} />
      <Route path='/scheduling/shifts/:id/view' component={Scheduling.Shifts.View} />

      <Route path='/scheduling/appointments' component={Scheduling.Appointments.List} />
      <Route path='/scheduling/appointments/new' component={Scheduling.Appointments.Dashboard} />
      <Route path='/scheduling/appointments/:id/view' component={Scheduling.Appointments.View} />
      <Route path='/scheduling/appointments/patients/:patient/new' component={Scheduling.Appointments.View} />

    </Route>
  </Route>
);
