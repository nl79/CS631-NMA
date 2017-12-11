import axios from "axios";

let prefix = '/api/scheduling';

export const SchedulingService = {

  search(q) {
    let url = `${prefix}/appointments/search`;
    return axios.get(url, {params: {q}});
  },

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

  appointmentStaff(id) {
    let url = `${prefix}/appointments/${id}/staff`;
    return axios.get(url);
  },

  assingAptStaff(apt, staff) {
    let url = `${prefix}/appointments/${apt}/staff/${staff}`;
    return axios.post(url);
  },

  unassignAptStaff(apt, staff) {
    let url = `${prefix}/appointments/${apt}/staff/${staff}`;
    return axios.delete(url);
  },

  unassignedAptStaff(id, params) {
    let url = `${prefix}/appointments/${id}/staff/unassigned`;
    return axios.get(url, {params});
  },

  appointmentRooms(id) {
    let url = `${prefix}/appointments/${id}/rooms`;
    return axios.get(url);
  },

  assingAptRoom(apt, room) {
    let url = `${prefix}/appointments/${apt}/rooms/${room}`;
    return axios.post(url);
  },

  unassignAptRoom(apt, room) {
    let url = `${prefix}/appointments/${apt}/rooms/${room}`;
    return axios.delete(url);
  },

  unassignedAptRooms(id, params) {
    let url = `${prefix}/appointments/${id}/rooms/unassigned`;
    return axios.get(url, {params});
  },

  appointmentsBy(type, id) {
    console.log('reportsBy', type, id);
    if(!type || !id) {
      return Promise.resolve({data: []});
    }
    let url = `${prefix}/appointments/reports/${type}/${id}`;

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

  unassignedStaff(id, params) {
    let url = `${prefix}/shifts/${id}/staff/unassigned`;
    return axios.get(url, {params});
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
