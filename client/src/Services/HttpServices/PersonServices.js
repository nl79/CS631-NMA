
import axios from "axios";


export const PersonService = {

    save(params) {

      console.log('Save', params);

      let url = '/api/person';
      return axios.post(url, params);
    }
}
