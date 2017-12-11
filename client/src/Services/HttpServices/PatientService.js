import axios from "axios";

let prefix = '/api/patients';

export const PatientService = {

  save(params) {
    let url = `${prefix}`;

    return axios.post(url, params);

  },

  get(id) {
    let url = `${prefix}/${id}`;
    return axios.get(url);
  },

  profile(id) {
    let url = `${prefix}/${id}/profile`;
    return axios.get(url);
  },

  list(params) {
    let url = `${prefix}/list`;
    return axios.get(url, params);
  },

  addCondition(patient, condition) {
    let url = `${prefix}/${patient}/condition`;
    return axios.post(url, {condition});
  },

  conditions(id) {
    let url = `${prefix}/${id}/conditions`;
    return axios.get(url);
  },

  staff(id) {
    let url = `${prefix}/${id}/staff`;
    return axios.get(url);
  },

  assignStaff(patient, staff) {
    let url = `${prefix}/${patient}/staff`;
    return axios.post(url, {staff});
  },

  unassignStaff(patient, staff) {
    let url = `${prefix}/${patient}/staff`;
    return axios.delete(url, {params: {staff}});
  },


  unassignedStaff(patient) {
    let url = `${prefix}/${patient}/staff/unassigned`;
    return axios.get(url);
  },

  facilities(id) {
    let url = `${prefix}/${id}/facilities`;
    return axios.get(url);
  },

  beds(id) {
    let url = `${prefix}/${id}/beds`;
    return axios.get(url);
  },
  assignBed(patient, bed) {
    let url = `${prefix}/${patient}/beds/${bed}`;
    return axios.post(url);
  },

  unassignBed(patient, bed) {
    let url = `${prefix}/${patient}/beds/${bed}`;
    return axios.delete(url);
  },

  unassignedBeds(patient) {
    let url = `${prefix}/${patient}/beds/unassigned`;
    return axios.get(url);
  }
}
