import axios from "axios";

let prefix = '/api/facilities';

export const FacilitiesService = {

  saveRoom(params) {
    let url = `${prefix}/rooms`;

    return axios.post(url, params);

  },

  addBed(room, params) {
    let url = `${prefix}/rooms/${room}/beds`;

    return axios.post(url, params);
  },

  getRoom(id) {
    let url = `${prefix}/rooms/${id}`;
    return axios.get(url);
  },

  listRooms(params) {
    let url = `${prefix}/rooms`;
    return axios.get(url, params);
  },

  getBeds(id) {
    let url = `${prefix}/rooms/${id}/beds`;
    return axios.get(url);
  },

  types() {
    let url = `${prefix}/types`;
    return axios.get(url);
  }
}
