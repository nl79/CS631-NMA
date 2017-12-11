import axios from "axios";

export class HttpService {
  constructor(provider) {
    this.http = provider;
  }

  get(api, options, name) {
    const startTime = new Date().getTime();
    return this.http.get(api, options).then((response) => {
      GA.Timing.APITime(api, startTime, name);
      return response;
    });
  }

  post(api, data, name, config) {
    const startTime = new Date().getTime();
    return this.http.post(api, data, config).then((response) => {
      GA.Timing.APITime(api, startTime, name);
      return response;
    })
  }
}

// Export an instance of the service.
export default new HttpService(axios);
