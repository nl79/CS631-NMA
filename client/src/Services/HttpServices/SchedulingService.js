import axios from "axios";

let prefix = '/api/scheduling';

export const SchedulingService = {

  list() {
    let url = `${prefix}/shifts`;

    return axios.get(url);
  },

  saveAppointment(params) {
    let url = `${prefix}/appointments`;

    return axios.post(url, params);
  },

  appointment(id) {
    let url = `${prefix}/appointments/${id}`;

    return axios.get(url);
  },

  appointments() {
    let url = `${prefix}/appointments`;

    return axios.get(url);
  },

  saveShift(params) {
    let url = `${prefix}/shifts`;

    return axios.post(url, params);
  },

  getShift(id) {
    let url = `${prefix}/shifts/${id}`;
    return axios.get(url);
  },

  shiftStaff(id) {
    let url = `${prefix}/shifts/${id}/staff`;
    return axios.get(url);
  },

  unassignedStaff(id) {
    let url = `${prefix}/shifts/${id}/staff/unassigned`;
    return axios.get(url);
  },
  assignStaff(shift, staff) {
    let url = `${prefix}/shifts/${shift}/staff/${staff}`;
    return axios.post(url);
  },
  unassignStaff(shift, staff) {
    let url = `${prefix}/shifts/${shift}/staff/${staff}`;
    return axios.delete(url);
  },
}
